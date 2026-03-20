<?php

namespace App\Http\Controllers;

use App\Models\MarketingPage;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SeoPageController extends Controller
{
    public function index(?string $any = null): View
    {
        $path = '/'.ltrim((string) $any, '/');

        if ($path === '//') {
            $path = '/';
        }

        return view('app', [
            'seo' => $this->buildSeoPayload($path),
        ]);
    }

    public function sitemap(): Response
    {
        $pages = collect([
            [
                'loc' => $this->absoluteUrl('/'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => $this->absoluteUrl('/learn-more'),
                'lastmod' => $this->marketingLastModified('learn-more'),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ],
        ])->merge(
            Service::query()
                ->where('enabled', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function (Service $service): array {
                    return [
                        'loc' => $this->absoluteUrl("/services/{$service->slug}"),
                        'lastmod' => $this->serviceLastModified($service),
                        'changefreq' => 'weekly',
                        'priority' => '0.8',
                    ];
                }),
        );

        $xml = view('sitemap', [
            'pages' => $pages,
        ])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * @return array{
     *     title: string,
     *     description: string,
     *     canonical: string,
     *     robots: string,
     *     image: string,
     *     siteName: string,
     *     type: string,
     *     jsonLd: array<int, array<string, mixed>>
     * }
     */
    private function buildSeoPayload(string $path): array
    {
        $settings = SiteSetting::query()->first();
        $siteName = $settings?->site_name ?: config('app.name', 'Banua Cloud Nusantara');
        $siteDescription = $settings?->site_description ?: 'Mitra infrastruktur IT, cloud, dan jaringan untuk bisnis di Indonesia';
        $companyName = $settings?->company_name ?: $siteName;
        $logoUrl = $this->absoluteAssetUrl($settings?->logo_url);
        $canonical = $this->absoluteUrl($path);
        $title = $siteName;
        $description = $siteDescription;
        $type = 'website';
        $robots = str_starts_with($path, '/admin') ? 'noindex, nofollow' : 'index, follow';

        if ($path === '/learn-more') {
            $marketingPage = MarketingPage::query()->where('page_key', 'learn-more')->first();
            $title = "Profil Layanan {$siteName}";
            $description = data_get($marketingPage?->payload, 'heroDescription', $siteDescription);
            $type = 'article';
        } elseif (str_starts_with($path, '/services/')) {
            $slug = trim(substr($path, strlen('/services/')), '/');
            $service = Service::query()->where('slug', $slug)->first();
            $marketingPage = MarketingPage::query()->where('page_key', "service:{$slug}")->first();

            if ($service) {
                $title = "{$service->name} | {$siteName}";
                $description = (string) (data_get($marketingPage?->payload, 'heroDescription') ?: $service->description ?: $siteDescription);
                $type = 'article';
            }
        } elseif (str_starts_with($path, '/admin')) {
            $title = "Admin Panel | {$siteName}";
            $description = "Area admin {$siteName}.";
        }

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => $robots,
            'image' => $logoUrl ?: $this->absoluteUrl('/favicon.svg'),
            'siteName' => $siteName,
            'type' => $type,
            'jsonLd' => $this->buildJsonLd(
                path: $path,
                siteName: $siteName,
                companyName: $companyName,
                description: $description,
                canonical: $canonical,
                image: $logoUrl ?: $this->absoluteUrl('/favicon.svg'),
            ),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildJsonLd(
        string $path,
        string $siteName,
        string $companyName,
        string $description,
        string $canonical,
        string $image,
    ): array {
        $organization = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $companyName,
            'url' => $this->absoluteUrl('/'),
            'logo' => $image,
            'description' => $description,
        ];

        $website = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => $this->absoluteUrl('/'),
            'description' => $description,
        ];

        if ($path === '/learn-more') {
            return [
                $organization,
                $website,
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'AboutPage',
                    'name' => "Profil Layanan {$siteName}",
                    'url' => $canonical,
                    'description' => $description,
                ],
            ];
        }

        if (str_starts_with($path, '/services/')) {
            $slug = trim(substr($path, strlen('/services/')), '/');
            $service = Service::query()->where('slug', $slug)->first();

            if ($service) {
                return [
                    $organization,
                    [
                        '@context' => 'https://schema.org',
                        '@type' => 'Service',
                        'name' => $service->name,
                        'description' => $description,
                        'provider' => [
                            '@type' => 'Organization',
                            'name' => $companyName,
                            'url' => $this->absoluteUrl('/'),
                        ],
                        'url' => $canonical,
                    ],
                    [
                        '@context' => 'https://schema.org',
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            [
                                '@type' => 'ListItem',
                                'position' => 1,
                                'name' => 'Beranda',
                                'item' => $this->absoluteUrl('/'),
                            ],
                            [
                                '@type' => 'ListItem',
                                'position' => 2,
                                'name' => $service->name,
                                'item' => $canonical,
                            ],
                        ],
                    ],
                ];
            }
        }

        return [
            $organization,
            $website,
        ];
    }

    private function absoluteUrl(string $path): string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        $normalizedPath = '/'.ltrim($path, '/');

        if ($normalizedPath === '//') {
            $normalizedPath = '/';
        }

        return $normalizedPath === '/'
            ? $baseUrl
            : $baseUrl.$normalizedPath;
    }

    private function absoluteAssetUrl(?string $path): string
    {
        if (! is_string($path) || trim($path) === '') {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return $this->absoluteUrl($path);
    }

    private function marketingLastModified(string $pageKey): string
    {
        $marketingPage = MarketingPage::query()->where('page_key', $pageKey)->first();

        return $this->toAtomString($marketingPage?->updated_at);
    }

    private function serviceLastModified(Service $service): string
    {
        $marketingPage = MarketingPage::query()->where('page_key', "service:{$service->slug}")->first();
        $updatedAt = collect([
            $service->updated_at,
            $marketingPage?->updated_at,
        ])->filter()->sortDesc()->first();

        return $this->toAtomString($updatedAt);
    }

    private function toAtomString(mixed $value): string
    {
        if ($value instanceof Carbon) {
            return $value->toAtomString();
        }

        return now()->toAtomString();
    }
}
