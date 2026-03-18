<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'role',
    'company',
    'content',
    'avatar',
    'sort_order',
])]
class Testimonial extends Model
{
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
