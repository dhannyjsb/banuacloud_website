<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'visitor_token',
    'path',
    'route_name',
    'page_title',
    'referrer_url',
    'referrer_host',
    'source',
    'medium',
    'utm_campaign',
    'ip_address',
    'user_agent',
    'country_code',
    'country_name',
    'city_name',
    'isp_name',
    'organization_name',
    'autonomous_system_number',
    'autonomous_system_organization',
    'visited_at',
])]
class VisitorVisit extends Model
{
    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
        ];
    }
}
