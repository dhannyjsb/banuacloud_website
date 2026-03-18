<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'page_key',
    'payload',
])]
class MarketingPage extends Model
{
    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}
