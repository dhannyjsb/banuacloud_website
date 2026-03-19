<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeatureItem;
use App\Models\HeroContent;
use App\Models\MarketingPage;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;

class SiteDataController extends Controller
{
    public function settings(): JsonResponse
    {
        return response()->json([
            'settings' => $this->formatSettings(),
        ]);
    }

    public function bootstrap(): JsonResponse
    {
        $services = Service::query()
            ->with('plans')
            ->where('enabled', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'settings' => $this->formatSettings(),
            'heroContent' => $this->formatHeroContent(),
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
            'services' => $services->map(fn(Service $service): array => $this->formatService($service))->all(),
        ]);
    }

    public function learnMore(): JsonResponse
    {
        return response()->json(
            $this->marketingPayload('learn-more'),
        );
    }

    public function serviceDetail(string $slug): JsonResponse
    {
        $payload = $this->marketingPayload("service:{$slug}");
        $marketingPricingCards = collect($payload['pricingCards'] ?? []);

        $service = Service::query()
            ->with('plans')
            ->where('slug', $slug)
            ->first();

        if ($service && $service->plans->isNotEmpty()) {
            $servicePricingCards = $service->plans
                ->map(fn($plan): array => [
                    'name' => $plan->name,
                    'price' => number_format($plan->price, 0, ',', '.'),
                    'period' => $plan->period,
                    'specs' => collect($plan->specs ?? [])
                        ->mapWithKeys(fn($value, $key): array => [(string) $key => (string) $value])
                        ->all(),
                    'features' => array_map('strval', $plan->features ?? []),
                    'popular' => $plan->popular,
                    'color' => $plan->color,
                ])
                ->values();

            if ($marketingPricingCards->count() > $servicePricingCards->count()) {
                $servicePricingCards = $servicePricingCards->concat(
                    $marketingPricingCards->slice($servicePricingCards->count())->values(),
                );
            }

            $payload['pricingCards'] = $servicePricingCards->all();
        }

        return response()->json($payload);
    }

    private function marketingPayload(string $pageKey): array
    {
        $page = MarketingPage::query()
            ->where('page_key', $pageKey)
            ->firstOrFail();

        return array_merge($this->marketingCtaDefaults($pageKey), $page->payload ?? []);
    }

    private function marketingCtaDefaults(string $pageKey): array
    {
        if ($pageKey === 'learn-more') {
            return [
                'ctaPrimaryTarget' => '/services/cloud-vps',
                'ctaSecondaryTarget' => '#contact',
            ];
        }

        return [
            'heroPrimaryTarget' => '#service-contact',
            'heroSecondaryTarget' => '#service-extra',
            'ctaPrimaryTarget' => '#service-contact',
            'ctaSecondaryTarget' => '#contact',
        ];
    }

    private function formatSettings(): array
    {
        $settings = SiteSetting::query()->first();

        if (! $settings) {
            return [
                'maintenanceMode' => false,
                'siteName' => 'Banua Cloud Nusantara',
                'siteDescription' => 'Mitra solusi IT tepercaya di Indonesia',
                'companyName' => 'PT Banua Cloud Nusantara',
                'companyEmail' => 'support@banuacloud.id',
                'companyPhone' => '+62 812-3456-7890',
                'companyWhatsapp' => '6281234567890',
                'companyAddress' => 'Indonesia',
                'socialInstagram' => '',
                'socialLinkedin' => '',
                'socialTwitter' => '',
                'socialFacebook' => '',
                'emailNotifications' => true,
                'orderAlerts' => true,
                'supportAlerts' => true,
                'twoFactorEnabled' => false,
                'sessionTimeout' => 30,
            ];
        }

        return [
            'maintenanceMode' => $settings->maintenance_mode,
            'siteName' => $settings->site_name,
            'siteDescription' => $settings->site_description,
            'companyName' => $settings->company_name,
            'companyEmail' => $settings->company_email,
            'companyPhone' => $settings->company_phone,
            'companyWhatsapp' => $settings->company_whatsapp,
            'companyAddress' => $settings->company_address,
            'socialInstagram' => $settings->social_instagram,
            'socialLinkedin' => $settings->social_linkedin,
            'socialTwitter' => $settings->social_twitter,
            'socialFacebook' => $settings->social_facebook,
            'emailNotifications' => $settings->email_notifications,
            'orderAlerts' => $settings->order_alerts,
            'supportAlerts' => $settings->support_alerts,
            'twoFactorEnabled' => $settings->two_factor_enabled,
            'sessionTimeout' => $settings->session_timeout,
        ];
    }

    private function formatHeroContent(): array
    {
        $hero = HeroContent::query()->first();

        if (! $hero) {
            return [
                'title' => 'Solusi Cloud untuk Bisnis Modern',
                'subtitle' => 'Rasakan performa super cepat dengan infrastruktur cloud tingkat enterprise kami. Solusi yang skalabel, aman, dan terpercaya disesuaikan dengan kebutuhan bisnis Anda.',
                'ctaPrimary' => 'Mulai Sekarang',
                'ctaSecondary' => 'Lihat Harga',
            ];
        }

        return [
            'title' => $hero->title,
            'subtitle' => $hero->subtitle,
            'ctaPrimary' => $hero->cta_primary,
            'ctaSecondary' => $hero->cta_secondary,
        ];
    }

    private function formatService(Service $service): array
    {
        return [
            'id' => (string) $service->id,
            'name' => $service->name,
            'slug' => $service->slug,
            'description' => $service->description,
            'icon' => $service->icon_key,
            'enabled' => $service->enabled,
            'plans' => $service->plans->map(fn($plan): array => [
                'id' => (string) $plan->id,
                'name' => $plan->name,
                'price' => $plan->price,
                'period' => $plan->period,
                'specs' => $plan->specs,
                'features' => $plan->features,
                'popular' => $plan->popular,
                'color' => $plan->color,
            ])->all(),
        ];
    }
}
