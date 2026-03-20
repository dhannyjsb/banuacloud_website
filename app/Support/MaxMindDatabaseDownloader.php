<?php

namespace App\Support;

use App\Models\MaxMindDatabaseSync;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PharData;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Throwable;

class MaxMindDatabaseDownloader
{
    private const DOWNLOAD_URL = 'https://download.maxmind.com/geoip/databases';

    public function __construct(private HttpFactory $http) {}

    public function download(array $editionIds, bool $force = false, string $trigger = 'manual'): array
    {
        $accountId = $this->accountId();
        $licenseKey = $this->licenseKey();

        if ($accountId === null || $licenseKey === null) {
            throw new RuntimeException('Isi MAXMIND_ACCOUNT_ID dan MAXMIND_LICENSE_KEY dulu. Download database MaxMind tidak bisa memakai API key web service saja.');
        }

        if (! class_exists(PharData::class)) {
            throw new RuntimeException('Extension Phar belum aktif, jadi archive MaxMind tar.gz belum bisa diekstrak otomatis.');
        }

        $results = [];

        foreach ($editionIds as $editionId) {
            $results[] = $this->downloadEdition($editionId, $accountId, $licenseKey, $force, $trigger);
        }

        return $results;
    }

    public function configuredEditionIds(?bool $includeIsp = null): array
    {
        $editionIds = ['GeoLite2-City', 'GeoLite2-ASN'];

        if ($includeIsp ?? $this->shouldIncludeIsp()) {
            $editionIds[] = 'GeoIP2-ISP';
        }

        return $editionIds;
    }

    public function ensureConfiguredDatabasesAvailable(?bool $includeIsp = null, string $trigger = 'auto'): array
    {
        if (! $this->shouldAutoDownload() || ! $this->canDownload()) {
            return [];
        }

        $editionIds = $this->configuredEditionIds($includeIsp);
        $missingEditionIds = array_values(array_filter(
            $editionIds,
            fn(string $editionId): bool => ! is_file($this->editionConfiguration($editionId)['path'])
                && ! $this->wasRecentlyChecked($editionId),
        ));

        if ($missingEditionIds === []) {
            return [];
        }

        return $this->download($missingEditionIds, trigger: $trigger);
    }

    public function canDownload(): bool
    {
        return $this->accountId() !== null
            && $this->licenseKey() !== null
            && class_exists(PharData::class);
    }

    public function shouldAutoDownload(): bool
    {
        return (bool) config('services.maxmind.auto_download', true);
    }

    public function shouldAutoUpdate(): bool
    {
        return (bool) config('services.maxmind.auto_update_enabled', true);
    }

    public function shouldIncludeIsp(): bool
    {
        return (bool) config('services.maxmind.auto_download_include_isp', false);
    }

    private function downloadEdition(string $editionId, int $accountId, string $licenseKey, bool $force, string $trigger): array
    {
        $configuration = $this->editionConfiguration($editionId);
        $destinationPath = $configuration['path'];

        if (! $force && is_file($destinationPath)) {
            return $this->persistSyncResult([
                'editionId' => $editionId,
                'path' => $destinationPath,
                'status' => 'skipped',
                'message' => 'File sudah ada. Pakai --force kalau mau download ulang.',
            ], $trigger, $force);
        }

        $temporaryDirectory = $this->temporaryDirectory();
        $archivePath = $temporaryDirectory . DIRECTORY_SEPARATOR . $editionId . '.tar.gz';
        $tarPath = $temporaryDirectory . DIRECTORY_SEPARATOR . $editionId . '.tar';
        $extractDirectory = $temporaryDirectory . DIRECTORY_SEPARATOR . 'extract';

        File::ensureDirectoryExists(dirname($destinationPath));
        File::ensureDirectoryExists($extractDirectory);

        try {
            $response = $this->http
                ->withBasicAuth((string) $accountId, $licenseKey)
                ->accept('application/gzip')
                ->timeout($this->downloadTimeout())
                ->connectTimeout($this->downloadConnectTimeout())
                ->get($this->databaseDownloadUrl($editionId), [
                    'suffix' => 'tar.gz',
                ]);

            if (! $response->successful()) {
                $body = $this->normalizeFailureMessage(trim($response->body()), $editionId);

                return $this->persistSyncResult([
                    'editionId' => $editionId,
                    'path' => $destinationPath,
                    'status' => 'failed',
                    'message' => $body !== ''
                        ? $body
                        : 'Download gagal dengan status HTTP ' . $response->status() . '.',
                ], $trigger, $force);
            }

            File::put($archivePath, $response->body());

            $this->extractArchive($archivePath, $tarPath, $extractDirectory);

            $databasePath = $this->findDatabaseFile($extractDirectory, $configuration['file']);

            if (! File::copy($databasePath, $destinationPath)) {
                throw new RuntimeException('File database berhasil didownload, tapi gagal dipindahkan ke lokasi tujuan.');
            }

            return $this->persistSyncResult([
                'editionId' => $editionId,
                'path' => $destinationPath,
                'status' => 'downloaded',
                'message' => 'Database berhasil diunduh.',
            ], $trigger, $force);
        } catch (Throwable $throwable) {
            return $this->persistSyncResult([
                'editionId' => $editionId,
                'path' => $destinationPath,
                'status' => 'failed',
                'message' => $this->normalizeFailureMessage($throwable->getMessage(), $editionId),
            ], $trigger, $force);
        } finally {
            File::deleteDirectory($temporaryDirectory);
        }
    }

    private function persistSyncResult(array $result, string $trigger, bool $force): array
    {
        if (! $this->canPersistSyncs()) {
            return $result;
        }

        $existingSync = MaxMindDatabaseSync::query()
            ->where('edition_id', $result['editionId'])
            ->first();

        $downloadedAt = $existingSync?->downloaded_at;

        if (in_array($result['status'], ['downloaded', 'skipped'], true) && is_file($result['path'])) {
            $downloadedAt = Carbon::now();
        }

        MaxMindDatabaseSync::query()->updateOrCreate(
            ['edition_id' => $result['editionId']],
            [
                'database_path' => $result['path'],
                'status' => $result['status'],
                'file_exists' => is_file($result['path']),
                'message' => $result['message'],
                'metadata' => [
                    'trigger' => $trigger,
                    'forced' => $force,
                ],
                'checked_at' => Carbon::now(),
                'downloaded_at' => $downloadedAt,
            ],
        );

        return $result;
    }

    private function canPersistSyncs(): bool
    {
        return Schema::hasTable('max_mind_database_syncs');
    }

    private function wasRecentlyChecked(string $editionId): bool
    {
        if (! $this->canPersistSyncs()) {
            return false;
        }

        $existingSync = MaxMindDatabaseSync::query()
            ->where('edition_id', $editionId)
            ->first();

        if ($existingSync?->status !== 'failed' || $existingSync->checked_at === null) {
            return false;
        }

        return $existingSync->checked_at->greaterThan(now()->subHours(6));
    }

    private function extractArchive(string $archivePath, string $tarPath, string $extractDirectory): void
    {
        try {
            if (! is_file($tarPath)) {
                $compressedArchive = new PharData($archivePath);
                $compressedArchive->decompress();
            }

            $archive = new PharData($tarPath);
            $archive->extractTo($extractDirectory, null, true);
        } catch (Throwable $throwable) {
            throw new RuntimeException('Archive MaxMind gagal diekstrak: ' . $throwable->getMessage(), previous: $throwable);
        }
    }

    private function findDatabaseFile(string $extractDirectory, string $fileName): string
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($extractDirectory, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($iterator as $item) {
            if ($item->isFile() && $item->getFilename() === $fileName) {
                return $item->getPathname();
            }
        }

        throw new RuntimeException('File ' . $fileName . ' tidak ditemukan di archive hasil download.');
    }

    private function editionConfiguration(string $editionId): array
    {
        return match ($editionId) {
            'GeoLite2-City' => [
                'file' => 'GeoLite2-City.mmdb',
                'path' => (string) config('services.maxmind.database_path', storage_path('app/maxmind/GeoLite2-City.mmdb')),
            ],
            'GeoLite2-ASN' => [
                'file' => 'GeoLite2-ASN.mmdb',
                'path' => (string) config('services.maxmind.asn_database_path', storage_path('app/maxmind/GeoLite2-ASN.mmdb')),
            ],
            'GeoIP2-ISP' => [
                'file' => 'GeoIP2-ISP.mmdb',
                'path' => (string) config('services.maxmind.isp_database_path', storage_path('app/maxmind/GeoIP2-ISP.mmdb')),
            ],
            default => throw new RuntimeException('Edition MaxMind tidak dikenali: ' . $editionId),
        };
    }

    private function databaseDownloadUrl(string $editionId): string
    {
        return self::DOWNLOAD_URL . '/' . $editionId . '/download';
    }

    private function temporaryDirectory(): string
    {
        return storage_path('app/maxmind/tmp/' . bin2hex(random_bytes(12)));
    }

    private function accountId(): ?int
    {
        $accountId = config('services.maxmind.account_id');

        if ($accountId === null || $accountId === '') {
            return null;
        }

        return is_numeric($accountId) ? (int) $accountId : null;
    }

    private function licenseKey(): ?string
    {
        $licenseKey = config('services.maxmind.license_key');

        if (! is_string($licenseKey) || trim($licenseKey) === '') {
            return null;
        }

        return trim($licenseKey);
    }

    private function downloadTimeout(): int
    {
        return max((int) config('services.maxmind.timeout', 3), 60);
    }

    private function downloadConnectTimeout(): int
    {
        return max((int) config('services.maxmind.connect_timeout', 2), 10);
    }

    private function normalizeFailureMessage(string $message, string $editionId): string
    {
        $normalizedMessage = trim($message);

        if ($normalizedMessage === '') {
            return 'Download gagal tanpa pesan error dari server MaxMind.';
        }

        if (str_contains($normalizedMessage, 'Database edition') && str_contains($normalizedMessage, 'not found')) {
            return 'Edition ' . $editionId . ' tidak tersedia untuk kredensial ini. Pastikan MAXMIND_ACCOUNT_ID dan MAXMIND_LICENSE_KEY adalah kredensial database download MaxMind, bukan API key web service.';
        }

        return $normalizedMessage;
    }
}
