<?php

namespace App\Providers;

use App\Support\MaxMindDatabaseDownloader;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(MaxMindDatabaseDownloader $maxMindDatabaseDownloader): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            $maxMindDatabaseDownloader->ensureConfiguredDatabasesAvailable(trigger: 'bootstrap');
        } catch (\Throwable) {
        }
    }
}
