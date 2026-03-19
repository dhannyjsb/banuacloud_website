<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'maintenance_mode',
    'site_name',
    'site_description',
    'logo_url',
    'company_name',
    'company_email',
    'company_phone',
    'company_whatsapp',
    'company_address',
    'social_instagram',
    'social_linkedin',
    'social_twitter',
    'social_facebook',
    'email_notifications',
    'order_alerts',
    'support_alerts',
    'two_factor_enabled',
    'session_timeout',
])]
class SiteSetting extends Model
{
    protected function casts(): array
    {
        return [
            'maintenance_mode' => 'boolean',
            'email_notifications' => 'boolean',
            'order_alerts' => 'boolean',
            'support_alerts' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'session_timeout' => 'integer',
        ];
    }
}
