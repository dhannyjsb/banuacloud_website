<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'slug',
    'description',
    'icon_key',
    'enabled',
    'sort_order',
])]
class Service extends Model
{
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function plans(): HasMany
    {
        return $this->hasMany(ServicePlan::class)->orderBy('sort_order');
    }
}
