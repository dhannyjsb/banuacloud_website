<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'settings' => $this->formatSettings($this->getSettingsRecord()),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'maintenanceMode' => ['required', 'boolean'],
            'siteName' => ['required', 'string', 'max:255'],
            'siteDescription' => ['required', 'string', 'max:500'],
            'companyName' => ['required', 'string', 'max:255'],
            'companyEmail' => ['required', 'email', 'max:255'],
            'companyPhone' => ['required', 'string', 'max:50'],
            'companyWhatsapp' => ['required', 'string', 'max:50'],
            'companyAddress' => ['required', 'string', 'max:255'],
            'socialInstagram' => ['nullable', 'string', 'max:255'],
            'socialLinkedin' => ['nullable', 'string', 'max:255'],
            'socialTwitter' => ['nullable', 'string', 'max:255'],
            'socialFacebook' => ['nullable', 'string', 'max:255'],
            'emailNotifications' => ['required', 'boolean'],
            'orderAlerts' => ['required', 'boolean'],
            'supportAlerts' => ['required', 'boolean'],
            'twoFactorEnabled' => ['required', 'boolean'],
            'sessionTimeout' => ['required', 'integer', 'min:5', 'max:120'],
        ]);

        $settings = $this->getSettingsRecord();
        $settings->fill([
            'maintenance_mode' => $data['maintenanceMode'],
            'site_name' => $data['siteName'],
            'site_description' => $data['siteDescription'],
            'company_name' => $data['companyName'],
            'company_email' => $data['companyEmail'],
            'company_phone' => $data['companyPhone'],
            'company_whatsapp' => $data['companyWhatsapp'],
            'company_address' => $data['companyAddress'],
            'social_instagram' => $data['socialInstagram'] ?? '',
            'social_linkedin' => $data['socialLinkedin'] ?? '',
            'social_twitter' => $data['socialTwitter'] ?? '',
            'social_facebook' => $data['socialFacebook'] ?? '',
            'email_notifications' => $data['emailNotifications'],
            'order_alerts' => $data['orderAlerts'],
            'support_alerts' => $data['supportAlerts'],
            'two_factor_enabled' => $data['twoFactorEnabled'],
            'session_timeout' => $data['sessionTimeout'],
        ])->save();

        return response()->json([
            'settings' => $this->formatSettings($settings->fresh()),
        ]);
    }

    private function getSettingsRecord(): SiteSetting
    {
        return SiteSetting::query()->firstOrCreate([], [
            'maintenance_mode' => false,
            'site_name' => 'Banua Cloud',
            'site_description' => 'Mitra solusi IT tepercaya di Indonesia',
            'company_name' => 'PT Banua Cloud Teknologi',
            'company_email' => 'support@banuacloud.id',
            'company_phone' => '+62 812-3456-7890',
            'company_whatsapp' => '6281234567890',
            'company_address' => 'Indonesia',
            'social_instagram' => '',
            'social_linkedin' => '',
            'social_twitter' => '',
            'social_facebook' => '',
            'email_notifications' => true,
            'order_alerts' => true,
            'support_alerts' => true,
            'two_factor_enabled' => false,
            'session_timeout' => 30,
        ]);
    }

    private function formatSettings(SiteSetting $settings): array
    {
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
}
