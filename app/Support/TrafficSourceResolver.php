<?php

namespace App\Support;

use Illuminate\Support\Str;

class TrafficSourceResolver
{
    public function resolve(
        ?string $referrerUrl,
        ?string $utmSource,
        ?string $utmMedium,
        string $currentHost,
    ): array {
        $referrerHost = $this->extractHost($referrerUrl);
        $normalizedSource = $this->normalizeValue($utmSource);
        $normalizedMedium = $this->normalizeValue($utmMedium);

        if ($normalizedSource !== null) {
            return [
                'source' => Str::headline($normalizedSource),
                'medium' => $normalizedMedium ?? 'campaign',
                'referrerHost' => $referrerHost,
            ];
        }

        if ($referrerHost === null || $this->isInternalHost($referrerHost, $currentHost)) {
            return [
                'source' => 'Direct',
                'medium' => 'direct',
                'referrerHost' => $referrerHost,
            ];
        }

        foreach ($this->knownSources() as $needle => [$source, $medium]) {
            if (str_contains($referrerHost, $needle)) {
                return [
                    'source' => $source,
                    'medium' => $medium,
                    'referrerHost' => $referrerHost,
                ];
            }
        }

        return [
            'source' => 'Referral',
            'medium' => 'referral',
            'referrerHost' => $referrerHost,
        ];
    }

    private function extractHost(?string $referrerUrl): ?string
    {
        if ($referrerUrl === null || trim($referrerUrl) === '') {
            return null;
        }

        $host = parse_url($referrerUrl, PHP_URL_HOST);

        return is_string($host) ? Str::lower($host) : null;
    }

    private function isInternalHost(string $referrerHost, string $currentHost): bool
    {
        $normalizedCurrentHost = Str::lower($currentHost);

        return $referrerHost === $normalizedCurrentHost || Str::endsWith($referrerHost, '.'.$normalizedCurrentHost);
    }

    private function normalizeValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim(Str::lower($value));

        return $normalized === '' ? null : $normalized;
    }

    private function knownSources(): array
    {
        return [
            'google.' => ['Google', 'search'],
            'bing.' => ['Bing', 'search'],
            'yahoo.' => ['Yahoo', 'search'],
            'duckduckgo.' => ['DuckDuckGo', 'search'],
            'instagram.' => ['Instagram', 'social'],
            'facebook.' => ['Facebook', 'social'],
            'linkedin.' => ['LinkedIn', 'social'],
            'x.com' => ['X', 'social'],
            'twitter.' => ['X', 'social'],
            't.co' => ['X', 'social'],
            'wa.me' => ['WhatsApp', 'social'],
            'whatsapp.' => ['WhatsApp', 'social'],
            'youtube.' => ['YouTube', 'social'],
            'tiktok.' => ['TikTok', 'social'],
        ];
    }
}
