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
            MarketingPage::query()
                ->where('page_key', 'learn-more')
                ->firstOrFail()
                ->payload,
        );
    }

    public function serviceDetail(string $slug): JsonResponse
    {
        return response()->json(
            MarketingPage::query()
                ->where('page_key', "service:{$slug}")
                ->firstOrFail()
                ->payload,
        );
    }

    private function formatSettings(): array
    {
        $settings = SiteSetting::query()->first();

        if (! $settings) {
            return [
                'maintenanceMode' => false,
                'siteName' => 'Banua Cloud',
                'siteDescription' => 'Trusted IT Solutions Partner in Indonesia',
                'companyName' => 'PT Banua Cloud Teknologi',
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
