<?php

use App\Support\MaxMindDatabaseDownloader;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('maxmind:download {--include-isp : Coba download database ISP juga bila license Anda mendukung} {--force : Download ulang walau file lokal sudah ada}', function (MaxMindDatabaseDownloader $downloader): int {
    $editionIds = ['GeoLite2-City', 'GeoLite2-ASN'];

    if ((bool) $this->option('include-isp')) {
        $editionIds[] = 'GeoIP2-ISP';
    }

    $this->info('Menyiapkan download database MaxMind...');

    try {
        $results = $downloader->download($editionIds, force: (bool) $this->option('force'));
    } catch (Throwable $throwable) {
        $this->error($throwable->getMessage());

        return self::FAILURE;
    }

    $failedCount = 0;
    $usableCount = 0;

    foreach ($results as $result) {
        $label = $result['editionId'];
        $message = $result['message'];
        $path = $result['path'];

        if ($result['status'] === 'downloaded') {
            $usableCount++;
            $this->info($label . ' downloaded -> ' . $path);

            continue;
        }

        if ($result['status'] === 'skipped') {
            $usableCount++;
            $this->line($label . ' skipped -> ' . $path . ' (' . $message . ')');

            continue;
        }

        $failedCount++;
        $this->warn($label . ' failed -> ' . $message);
    }

    if ($usableCount === 0) {
        return self::FAILURE;
    }

    if ($failedCount > 0) {
        $this->warn('Sebagian database gagal diunduh, tapi file lain yang berhasil tetap bisa dipakai.');
    }

    $this->newLine();
    $this->comment('Selesai. Untuk setup cepat, cukup jalankan: php artisan maxmind:download');

    return self::SUCCESS;
})->purpose('Download database MaxMind lokal untuk geolocation dan ISP');

Schedule::call(function (): void {
    $downloader = app(MaxMindDatabaseDownloader::class);

    if (! $downloader->shouldAutoUpdate() || ! $downloader->canDownload()) {
        return;
    }

    $downloader->download($downloader->configuredEditionIds(), force: true, trigger: 'schedule');
})
    ->name('maxmind-weekly-refresh')
    ->weeklyOn(1, '02:00')
    ->withoutOverlapping()
    ->evenInMaintenanceMode();
