<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use App\Models\FaqItem;
use App\Models\FeatureItem;
use App\Models\HeroContent;
use App\Models\MarketingPage;
use App\Models\Testimonial;
use App\Support\AdminAuditLogger;
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
            'faqs' => ['present', 'array'],
            'faqs.*.question' => ['required', 'string', 'max:255'],
            'faqs.*.answer' => ['required', 'string', 'max:2000'],
            'caseStudies' => ['present', 'array'],
            'caseStudies.*.title' => ['required', 'string', 'max:255'],
            'caseStudies.*.clientName' => ['required', 'string', 'max:255'],
            'caseStudies.*.category' => ['required', 'string', 'max:100'],
            'caseStudies.*.summary' => ['required', 'string', 'max:500'],
            'caseStudies.*.challenge' => ['required', 'string', 'max:2000'],
            'caseStudies.*.solution' => ['required', 'string', 'max:2000'],
            'caseStudies.*.outcome' => ['required', 'string', 'max:2000'],
            'caseStudies.*.tags' => ['present', 'array'],
            'caseStudies.*.tags.*' => ['required', 'string', 'max:50'],
            'caseStudies.*.galleryImages' => ['present', 'array', 'size:3'],
            'caseStudies.*.galleryImages.*' => ['nullable', 'string', 'max:2048'],
            'caseStudies.*.isFeatured' => ['required', 'boolean'],
            'caseStudiesEnabled' => ['required', 'boolean'],
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

            FaqItem::query()->delete();
            foreach ($data['faqs'] as $index => $faq) {
                FaqItem::query()->create([
                    'question' => $faq['question'],
                    'answer' => $faq['answer'],
                    'sort_order' => $index,
                ]);
            }

            CaseStudy::query()->delete();
            foreach ($data['caseStudies'] as $index => $caseStudy) {
                CaseStudy::query()->create([
                    'title' => $caseStudy['title'],
                    'client_name' => $caseStudy['clientName'],
                    'category' => $caseStudy['category'],
                    'summary' => $caseStudy['summary'],
                    'challenge' => $caseStudy['challenge'],
                    'solution' => $caseStudy['solution'],
                    'outcome' => $caseStudy['outcome'],
                    'tags' => $caseStudy['tags'],
                    'gallery_images' => $this->normalizeGalleryImages($caseStudy['galleryImages'] ?? []),
                    'is_featured' => $caseStudy['isFeatured'],
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

                if ($marketingCta['pageKey'] === 'learn-more') {
                    $payload['caseStudiesEnabled'] = $data['caseStudiesEnabled'];
                }

                $page->payload = $payload;
                $page->save();
            }
        });

        AdminAuditLogger::record(
            $request->user(),
            'content',
            'updated',
            sprintf(
                'Updated hero, %d features, %d testimonials, %d FAQs, and %d case studies.',
                count($data['features']),
                count($data['testimonials']),
                count($data['faqs']),
                count($data['caseStudies']),
            ),
        );

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
            'faqs' => FaqItem::query()
                ->orderBy('sort_order')
                ->get()
                ->map(fn(FaqItem $faq): array => [
                    'id' => (string) $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                ])
                ->all(),
            'caseStudies' => CaseStudy::query()
                ->orderBy('sort_order')
                ->get()
                ->map(fn(CaseStudy $caseStudy): array => [
                    'id' => (string) $caseStudy->id,
                    'title' => $caseStudy->title,
                    'clientName' => $caseStudy->client_name,
                    'category' => $caseStudy->category,
                    'summary' => $caseStudy->summary,
                    'challenge' => $caseStudy->challenge,
                    'solution' => $caseStudy->solution,
                    'outcome' => $caseStudy->outcome,
                    'tags' => $caseStudy->tags ?? [],
                    'galleryImages' => $this->normalizeGalleryImages($caseStudy->gallery_images ?? []),
                    'isFeatured' => $caseStudy->is_featured,
                ])
                ->all(),
            'caseStudiesEnabled' => $this->caseStudiesEnabled(),
            'marketingCtas' => $this->marketingCtaPayload(),
        ];
    }

    private function caseStudiesEnabled(): bool
    {
        $payload = MarketingPage::query()
            ->where('page_key', 'learn-more')
            ->value('payload');

        if (! is_array($payload)) {
            return true;
        }

        return (bool) ($payload['caseStudiesEnabled'] ?? true);
    }

    private function normalizeGalleryImages(array $galleryImages): array
    {
        $images = collect($galleryImages)
            ->map(fn(mixed $image): string => is_string($image) ? trim($image) : '')
            ->pad(3, '')
            ->take(3)
            ->values()
            ->all();

        if (collect($images)->filter()->isEmpty()) {
            return [
                '/gallery/case-study-1.svg',
                '/gallery/case-study-2.svg',
                '/gallery/case-study-3.svg',
            ];
        }

        return $images;
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
