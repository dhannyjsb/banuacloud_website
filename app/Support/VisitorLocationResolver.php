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
                if (! $this->hasNetworkData($location)) {
                    $location = array_merge($location, $this->resolveFromNetworkDatabases($ipAddress));
                }

                return array_merge($this->emptyLocation(), $location);
            }
        }

        $location = $this->hasDatabase()
            ? $this->resolveFromDatabase($ipAddress)
            : $this->emptyLocation();

        if (! $this->hasNetworkData($location)) {
            $location = array_merge($location, $this->resolveFromNetworkDatabases($ipAddress));
        }

        return array_merge($this->emptyLocation(), $location);
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
            $insights = $client->insights($ipAddress);

            return [
                'countryCode' => $insights->country->isoCode,
                'countryName' => $insights->country->name,
                'cityName' => $insights->city->name,
                'ispName' => $this->nullableString($insights->traits->isp),
                'organizationName' => $this->nullableString($insights->traits->organization),
                'autonomousSystemNumber' => $insights->traits->autonomousSystemNumber,
                'autonomousSystemOrganization' => $this->nullableString($insights->traits->autonomousSystemOrganization),
            ];
        } catch (Throwable) {
            try {
                $city = $client->city($ipAddress);

                return [
                    'countryCode' => $city->country->isoCode,
                    'countryName' => $city->country->name,
                    'cityName' => $city->city->name,
                    'ispName' => $this->nullableString($city->traits->isp),
                    'organizationName' => $this->nullableString($city->traits->organization),
                    'autonomousSystemNumber' => $city->traits->autonomousSystemNumber,
                    'autonomousSystemOrganization' => $this->nullableString($city->traits->autonomousSystemOrganization),
                ];
            } catch (Throwable) {
                try {
                    $country = $client->country($ipAddress);

                    return [
                        'countryCode' => $country->country->isoCode,
                        'countryName' => $country->country->name,
                        'cityName' => null,
                        'ispName' => $this->nullableString($country->traits->isp),
                        'organizationName' => $this->nullableString($country->traits->organization),
                        'autonomousSystemNumber' => $country->traits->autonomousSystemNumber,
                        'autonomousSystemOrganization' => $this->nullableString($country->traits->autonomousSystemOrganization),
                    ];
                } catch (Throwable) {
                    return null;
                }
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
                'ispName' => $this->nullableString($city->traits->isp),
                'organizationName' => $this->nullableString($city->traits->organization),
                'autonomousSystemNumber' => $city->traits->autonomousSystemNumber,
                'autonomousSystemOrganization' => $this->nullableString($city->traits->autonomousSystemOrganization),
            ];
        } catch (Throwable) {
            try {
                $country = $reader->country($ipAddress);

                return [
                    'countryCode' => $country->country->isoCode,
                    'countryName' => $country->country->name,
                    'cityName' => null,
                    'ispName' => $this->nullableString($country->traits->isp),
                    'organizationName' => $this->nullableString($country->traits->organization),
                    'autonomousSystemNumber' => $country->traits->autonomousSystemNumber,
                    'autonomousSystemOrganization' => $this->nullableString($country->traits->autonomousSystemOrganization),
                ];
            } catch (Throwable) {
                return $this->emptyLocation();
            }
        } finally {
            $reader->close();
        }
    }

    private function resolveFromNetworkDatabases(string $ipAddress): array
    {
        if ($this->hasIspDatabase()) {
            $network = $this->resolveFromIspDatabase($ipAddress);

            if ($network !== null) {
                return $network;
            }
        }

        if ($this->hasAsnDatabase()) {
            $network = $this->resolveFromAsnDatabase($ipAddress);

            if ($network !== null) {
                return $network;
            }
        }

        return $this->emptyNetwork();
    }

    private function resolveFromIspDatabase(string $ipAddress): ?array
    {
        $reader = new Reader($this->ispDatabasePath());

        try {
            $record = $reader->isp($ipAddress);

            return [
                'ispName' => $this->nullableString($record->isp),
                'organizationName' => $this->nullableString($record->organization),
                'autonomousSystemNumber' => $record->autonomousSystemNumber,
                'autonomousSystemOrganization' => $this->nullableString($record->autonomousSystemOrganization),
            ];
        } catch (Throwable) {
            return null;
        } finally {
            $reader->close();
        }
    }

    private function resolveFromAsnDatabase(string $ipAddress): ?array
    {
        $reader = new Reader($this->asnDatabasePath());

        try {
            $record = $reader->asn($ipAddress);
            $organization = $this->nullableString($record->autonomousSystemOrganization);

            return [
                'ispName' => $organization,
                'organizationName' => null,
                'autonomousSystemNumber' => $record->autonomousSystemNumber,
                'autonomousSystemOrganization' => $organization,
            ];
        } catch (Throwable) {
            return null;
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

    private function hasIspDatabase(): bool
    {
        return class_exists(Reader::class) && is_file($this->ispDatabasePath());
    }

    private function ispDatabasePath(): string
    {
        return (string) config('services.maxmind.isp_database_path', storage_path('app/maxmind/GeoIP2-ISP.mmdb'));
    }

    private function hasAsnDatabase(): bool
    {
        return class_exists(Reader::class) && is_file($this->asnDatabasePath());
    }

    private function asnDatabasePath(): string
    {
        return (string) config('services.maxmind.asn_database_path', storage_path('app/maxmind/GeoLite2-ASN.mmdb'));
    }

    private function isPublicIp(string $ipAddress): bool
    {
        return filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    private function hasNetworkData(array $location): bool
    {
        return filled($location['ispName'] ?? null)
            || filled($location['organizationName'] ?? null)
            || ($location['autonomousSystemNumber'] ?? null) !== null
            || filled($location['autonomousSystemOrganization'] ?? null);
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $normalizedValue = trim($value);

        return $normalizedValue === '' ? null : $normalizedValue;
    }

    private function emptyNetwork(): array
    {
        return [
            'ispName' => null,
            'organizationName' => null,
            'autonomousSystemNumber' => null,
            'autonomousSystemOrganization' => null,
        ];
    }

    private function emptyLocation(): array
    {
        return array_merge([
            'countryCode' => null,
            'countryName' => null,
            'cityName' => null,
        ], $this->emptyNetwork());
    }
}
