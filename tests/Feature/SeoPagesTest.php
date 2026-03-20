<?php

namespace Tests\Feature;

use App\Models\MarketingPage;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_default_seo_tags(): void
    {
        SiteSetting::query()->create([
            'site_name' => 'Banua Cloud Nusantara',
            'site_description' => 'Solusi cloud dan infrastruktur untuk bisnis Indonesia.',
            'company_name' => 'Banua Cloud Nusantara',
            'company_email' => 'support@banuacloud.id',
            'company_phone' => '+62 812-3456-7890',
            'company_whatsapp' => '6281234567890',
            'company_address' => 'Banjarmasin, Indonesia',
            'email_notifications' => true,
            'order_alerts' => true,
            'support_alerts' => true,
            'two_factor_enabled' => false,
            'session_timeout' => 30,
        ]);

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('<title>Banua Cloud Nusantara</title>', false)
            ->assertSee('meta name="description" content="Solusi cloud dan infrastruktur untuk bisnis Indonesia."', false)
            ->assertSee('link rel="canonical" href="http://localhost"', false);
    }

    public function test_service_page_renders_service_specific_seo_tags(): void
    {
        SiteSetting::query()->create([
            'site_name' => 'Banua Cloud Nusantara',
            'site_description' => 'Solusi cloud dan infrastruktur untuk bisnis Indonesia.',
            'company_name' => 'Banua Cloud Nusantara',
            'company_email' => 'support@banuacloud.id',
            'company_phone' => '+62 812-3456-7890',
            'company_whatsapp' => '6281234567890',
            'company_address' => 'Banjarmasin, Indonesia',
            'email_notifications' => true,
            'order_alerts' => true,
            'support_alerts' => true,
            'two_factor_enabled' => false,
            'session_timeout' => 30,
        ]);

        Service::query()->create([
            'name' => 'Cloud VPS',
            'slug' => 'cloud-vps',
            'description' => 'Server cloud cepat untuk kebutuhan bisnis.',
            'icon_key' => 'server',
            'enabled' => true,
            'sort_order' => 0,
        ]);

        MarketingPage::query()->create([
            'page_key' => 'service:cloud-vps',
            'payload' => [
                'heroDescription' => 'Cloud VPS cepat, aman, dan siap scale untuk bisnis modern.',
            ],
        ]);

        $response = $this->get('/services/cloud-vps');

        $response
            ->assertOk()
            ->assertSee('<title>Cloud VPS | Banua Cloud Nusantara</title>', false)
            ->assertSee('meta property="og:type" content="article"', false)
            ->assertSee('meta name="description" content="Cloud VPS cepat, aman, dan siap scale untuk bisnis modern."', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('"@type":"Service"', false);
    }

    public function test_sitemap_lists_public_pages_and_enabled_services(): void
    {
        Service::query()->create([
            'name' => 'Cloud VPS',
            'slug' => 'cloud-vps',
            'description' => 'Server cloud cepat untuk kebutuhan bisnis.',
            'icon_key' => 'server',
            'enabled' => true,
            'sort_order' => 0,
        ]);

        Service::query()->create([
            'name' => 'Internal Service',
            'slug' => 'internal-service',
            'description' => 'Tidak untuk publik.',
            'icon_key' => 'shield',
            'enabled' => false,
            'sort_order' => 1,
        ]);

        $response = $this->get('/sitemap.xml');

        $response
            ->assertOk()
            ->assertHeader('content-type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>http://localhost</loc>', false)
            ->assertSee('<loc>http://localhost/learn-more</loc>', false)
            ->assertSee('<loc>http://localhost/services/cloud-vps</loc>', false)
            ->assertDontSee('internal-service', false);
    }
}
