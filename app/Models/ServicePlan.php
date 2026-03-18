<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'service_id',
    'name',
    'price',
    'period',
    'specs',
    'features',
    'popular',
    'color',
    'sort_order',
])]
class ServicePlan extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'specs' => 'array',
            'features' => 'array',
            'popular' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
