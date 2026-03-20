<?php

namespace App\Support;

use GeoIp2\Database\Reader;
use GeoIp2\WebService\Client as WebServiceClient;
use Throwable;

class VisitorLocationResolver
{
    public function isEnabled(): bool
    {
        return $this->lookupMode() !== 'disabled';
    }

    public function lookupMode(): string
    {
        if ($this->hasWebService()) {
            return 'web_service';
        }

        if ($this->hasDatabase()) {
            return 'database';
        }

        return 'disabled';
    }

    public function hasDatabase(): bool
    {
        return class_exists(Reader::class) && is_file($this->databasePath());
    }

    public function resolve(?string $ipAddress): array
    {
        if ($ipAddress === null || ! $this->isPublicIp($ipAddress)) {
            return $this->emptyLocation();
        }

        if ($this->hasWebService()) {
            $location = $this->resolveFromWebService($ipAddress);

            if ($location !== null) {
                return $location;
            }
        }

        if ($this->hasDatabase()) {
            return $this->resolveFromDatabase($ipAddress);
        }

        return $this->emptyLocation();
    }

    private function hasWebService(): bool
    {
        return class_exists(WebServiceClient::class)
            && $this->accountId() !== null
            && $this->licenseKey() !== null;
    }

    private function resolveFromWebService(string $ipAddress): ?array
    {
        $accountId = $this->accountId();
        $licenseKey = $this->licenseKey();

        if ($accountId === null || $licenseKey === null) {
            return null;
        }

        $client = new WebServiceClient(
            $accountId,
            $licenseKey,
            ['en'],
            [
                'host' => (string) config('services.maxmind.host', 'geoip.maxmind.com'),
                'timeout' => (float) config('services.maxmind.timeout', 3),
                'connectTimeout' => (float) config('services.maxmind.connect_timeout', 2),
            ],
        );

        try {
            $city = $client->city($ipAddress);

            return [
                'countryCode' => $city->country->isoCode,
                'countryName' => $city->country->name,
                'cityName' => $city->city->name,
            ];
        } catch (Throwable) {
            try {
                $country = $client->country($ipAddress);

                return [
                    'countryCode' => $country->country->isoCode,
                    'countryName' => $country->country->name,
                    'cityName' => null,
                ];
            } catch (Throwable) {
                return null;
            }
        }
    }

    private function resolveFromDatabase(string $ipAddress): array
    {
        $reader = new Reader($this->databasePath());

        try {
            $city = $reader->city($ipAddress);

            return [
                'countryCode' => $city->country->isoCode,
                'countryName' => $city->country->name,
                'cityName' => $city->city->name,
            ];
        } catch (Throwable) {
            try {
                $country = $reader->country($ipAddress);

                return [
                    'countryCode' => $country->country->isoCode,
                    'countryName' => $country->country->name,
                    'cityName' => null,
                ];
            } catch (Throwable) {
                return $this->emptyLocation();
            }
        } finally {
            $reader->close();
        }
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

        return $licenseKey;
    }

    private function databasePath(): string
    {
        return (string) config('services.maxmind.database_path', storage_path('app/maxmind/GeoLite2-City.mmdb'));
    }

    private function isPublicIp(string $ipAddress): bool
    {
        return filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    private function emptyLocation(): array
    {
        return [
            'countryCode' => null,
            'countryName' => null,
            'cityName' => null,
        ];
    }
}
