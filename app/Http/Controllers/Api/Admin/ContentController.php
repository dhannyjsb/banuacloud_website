<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureItem;
use App\Models\HeroContent;
use App\Models\MarketingPage;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json($this->payload());
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'heroContent.title' => ['required', 'string', 'max:255'],
            'heroContent.subtitle' => ['required', 'string', 'max:500'],
            'heroContent.ctaPrimary' => ['required', 'string', 'max:100'],
            'heroContent.ctaSecondary' => ['required', 'string', 'max:100'],
            'features' => ['present', 'array'],
            'features.*.title' => ['required', 'string', 'max:255'],
            'features.*.description' => ['required', 'string', 'max:500'],
            'features.*.icon' => ['required', 'string', 'max:100'],
            'testimonials' => ['present', 'array'],
            'testimonials.*.name' => ['required', 'string', 'max:255'],
            'testimonials.*.role' => ['required', 'string', 'max:255'],
            'testimonials.*.company' => ['required', 'string', 'max:255'],
            'testimonials.*.content' => ['required', 'string', 'max:1000'],
            'testimonials.*.avatar' => ['nullable', 'string', 'max:255'],
            'marketingCtas' => ['present', 'array'],
            'marketingCtas.*.pageKey' => ['required', 'string', 'max:255'],
            'marketingCtas.*.heroPrimaryTarget' => ['nullable', 'string', 'max:255'],
            'marketingCtas.*.heroSecondaryTarget' => ['nullable', 'string', 'max:255'],
            'marketingCtas.*.ctaPrimaryTarget' => ['required', 'string', 'max:255'],
            'marketingCtas.*.ctaSecondaryTarget' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data): void {
            $hero = HeroContent::query()->firstOrCreate([], [
                'title' => '',
                'subtitle' => '',
                'cta_primary' => '',
                'cta_secondary' => '',
            ]);

            $hero->fill([
                'title' => $data['heroContent']['title'],
                'subtitle' => $data['heroContent']['subtitle'],
                'cta_primary' => $data['heroContent']['ctaPrimary'],
                'cta_secondary' => $data['heroContent']['ctaSecondary'],
            ])->save();

            FeatureItem::query()->delete();
            foreach ($data['features'] as $index => $feature) {
                FeatureItem::query()->create([
                    'title' => $feature['title'],
                    'description' => $feature['description'],
                    'icon' => $feature['icon'],
                    'sort_order' => $index,
                ]);
            }

            Testimonial::query()->delete();
            foreach ($data['testimonials'] as $index => $testimonial) {
                Testimonial::query()->create([
                    'name' => $testimonial['name'],
                    'role' => $testimonial['role'],
                    'company' => $testimonial['company'],
                    'content' => $testimonial['content'],
                    'avatar' => $testimonial['avatar'] ?? null,
                    'sort_order' => $index,
                ]);
            }

            foreach ($data['marketingCtas'] as $marketingCta) {
                $page = MarketingPage::query()->where('page_key', $marketingCta['pageKey'])->first();

                if (! $page) {
                    continue;
                }

                $payload = $page->payload ?? [];
                $payload['heroPrimaryTarget'] = $marketingCta['heroPrimaryTarget'] ?? '';
                $payload['heroSecondaryTarget'] = $marketingCta['heroSecondaryTarget'] ?? '';
                $payload['ctaPrimaryTarget'] = $marketingCta['ctaPrimaryTarget'];
                $payload['ctaSecondaryTarget'] = $marketingCta['ctaSecondaryTarget'];

                $page->payload = $payload;
                $page->save();
            }
        });

        return response()->json($this->payload());
    }

    private function payload(): array
    {
        $hero = HeroContent::query()->first();

        return [
            'heroContent' => [
                'title' => $hero?->title ?? 'Solusi Cloud untuk Bisnis Modern',
                'subtitle' => $hero?->subtitle ?? 'Rasakan performa super cepat dengan infrastruktur cloud tingkat enterprise kami. Solusi yang skalabel, aman, dan terpercaya disesuaikan dengan kebutuhan bisnis Anda.',
                'ctaPrimary' => $hero?->cta_primary ?? 'Mulai Sekarang',
                'ctaSecondary' => $hero?->cta_secondary ?? 'Lihat Harga',
            ],
            'features' => FeatureItem::query()
                ->orderBy('sort_order')
                ->get()
                ->map(fn(FeatureItem $feature): array => [
                    'id' => (string) $feature->id,
                    'title' => $feature->title,
                    'description' => $feature->description,
                    'icon' => $feature->icon,
                ])
                ->all(),
            'testimonials' => Testimonial::query()
                ->orderBy('sort_order')
                ->get()
                ->map(fn(Testimonial $testimonial): array => [
                    'id' => (string) $testimonial->id,
                    'name' => $testimonial->name,
                    'role' => $testimonial->role,
                    'company' => $testimonial->company,
                    'content' => $testimonial->content,
                    'avatar' => $testimonial->avatar,
                ])
                ->all(),
            'marketingCtas' => $this->marketingCtaPayload(),
        ];
    }

    private function marketingCtaPayload(): array
    {
        $defaults = [
            'learn-more' => [
                'pageTitle' => 'Learn More',
                'supportsHeroCtas' => false,
                'heroPrimaryTarget' => '',
                'heroSecondaryTarget' => '',
                'ctaPrimaryTarget' => '/services/cloud-vps',
                'ctaSecondaryTarget' => '#contact',
            ],
            'service:cloud-vps' => [
                'pageTitle' => 'Cloud VPS',
                'supportsHeroCtas' => true,
                'heroPrimaryTarget' => '#service-contact',
                'heroSecondaryTarget' => '#service-pricing',
                'ctaPrimaryTarget' => '#service-contact',
                'ctaSecondaryTarget' => '#contact',
            ],
            'service:web-hosting' => [
                'pageTitle' => 'Web Hosting',
                'supportsHeroCtas' => true,
                'heroPrimaryTarget' => '#service-contact',
                'heroSecondaryTarget' => '#service-pricing',
                'ctaPrimaryTarget' => '#service-contact',
                'ctaSecondaryTarget' => '#contact',
            ],
            'service:domain' => [
                'pageTitle' => 'Layanan Domain',
                'supportsHeroCtas' => true,
                'heroPrimaryTarget' => '#service-extra',
                'heroSecondaryTarget' => '#service-contact',
                'ctaPrimaryTarget' => '#service-contact',
                'ctaSecondaryTarget' => '#contact',
            ],
            'service:backup' => [
                'pageTitle' => 'Solusi Backup',
                'supportsHeroCtas' => true,
                'heroPrimaryTarget' => '#service-contact',
                'heroSecondaryTarget' => '#service-pricing',
                'ctaPrimaryTarget' => '#service-contact',
                'ctaSecondaryTarget' => '#contact',
            ],
            'service:app-development' => [
                'pageTitle' => 'Pengembangan Aplikasi',
                'supportsHeroCtas' => true,
                'heroPrimaryTarget' => '#service-contact',
                'heroSecondaryTarget' => '#service-extra',
                'ctaPrimaryTarget' => '#service-contact',
                'ctaSecondaryTarget' => '#contact',
            ],
            'service:it-consulting' => [
                'pageTitle' => 'Konsultasi IT',
                'supportsHeroCtas' => true,
                'heroPrimaryTarget' => '#service-contact',
                'heroSecondaryTarget' => '#service-extra',
                'ctaPrimaryTarget' => '#service-contact',
                'ctaSecondaryTarget' => '#contact',
            ],
        ];

        $pages = MarketingPage::query()
            ->whereIn('page_key', array_keys($defaults))
            ->get()
            ->keyBy('page_key');

        return collect($defaults)
            ->map(function (array $default, string $pageKey) use ($pages): array {
                $payload = $pages->get($pageKey)?->payload ?? [];

                return [
                    'pageKey' => $pageKey,
                    'pageTitle' => $default['pageTitle'],
                    'supportsHeroCtas' => $default['supportsHeroCtas'],
                    'heroPrimaryTarget' => $payload['heroPrimaryTarget'] ?? $default['heroPrimaryTarget'],
                    'heroSecondaryTarget' => $payload['heroSecondaryTarget'] ?? $default['heroSecondaryTarget'],
                    'ctaPrimaryTarget' => $payload['ctaPrimaryTarget'] ?? $default['ctaPrimaryTarget'],
                    'ctaSecondaryTarget' => $payload['ctaSecondaryTarget'] ?? $default['ctaSecondaryTarget'],
                ];
            })
            ->values()
            ->all();
    }
}
