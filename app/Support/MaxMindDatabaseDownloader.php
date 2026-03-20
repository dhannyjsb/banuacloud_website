<?php

namespace App\Support;

use App\Models\MaxMindDatabaseSync;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Throwable;
use ZipArchive;

class MaxMindDatabaseDownloader
{
    private const DOWNLOAD_URL = 'https://download.maxmind.com/app/geoip_download';

    public function __construct(private HttpFactory $http) {}

    public function download(array $editionIds, bool $force = false, string $trigger = 'manual'): array
    {
        $licenseKey = $this->licenseKey();

        if ($licenseKey === null) {
            throw new RuntimeException('Isi MAXMIND_LICENSE_KEY atau MAXMIND_API_KEY dulu, baru jalankan command ini.');
        }

        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('PHP extension zip belum aktif, jadi archive MaxMind belum bisa diekstrak otomatis.');
        }

        $results = [];

        foreach ($editionIds as $editionId) {
            $results[] = $this->downloadEdition($editionId, $licenseKey, $force, $trigger);
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
        return $this->licenseKey() !== null && class_exists(ZipArchive::class);
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

    private function downloadEdition(string $editionId, string $licenseKey, bool $force, string $trigger): array
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
        $archivePath = $temporaryDirectory . DIRECTORY_SEPARATOR . $editionId . '.zip';
        $extractDirectory = $temporaryDirectory . DIRECTORY_SEPARATOR . 'extract';

        File::ensureDirectoryExists(dirname($destinationPath));
        File::ensureDirectoryExists($extractDirectory);

        try {
            $response = $this->http
                ->accept('application/zip')
                ->timeout($this->downloadTimeout())
                ->connectTimeout($this->downloadConnectTimeout())
                ->get(self::DOWNLOAD_URL, [
                    'edition_id' => $editionId,
                    'license_key' => $licenseKey,
                    'suffix' => 'zip',
                ]);

            if (! $response->successful()) {
                $body = trim($response->body());

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

            $this->extractArchive($archivePath, $extractDirectory);

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
                'message' => $throwable->getMessage(),
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

    private function extractArchive(string $archivePath, string $extractDirectory): void
    {
        $zipArchive = new ZipArchive;
        $result = $zipArchive->open($archivePath);

        if ($result !== true) {
            throw new RuntimeException('Archive MaxMind gagal dibuka.');
        }

        try {
            if (! $zipArchive->extractTo($extractDirectory)) {
                throw new RuntimeException('Archive MaxMind gagal diekstrak.');
            }
        } finally {
            $zipArchive->close();
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

    private function temporaryDirectory(): string
    {
        return storage_path('app/maxmind/tmp/' . bin2hex(random_bytes(12)));
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
}
