<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'client_name',
    'category',
    'summary',
    'challenge',
    'solution',
    'outcome',
    'tags',
    'gallery_images',
    'is_featured',
    'sort_order',
])]
class CaseStudy extends Model
{
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'gallery_images' => 'array',
            'is_featured' => 'boolean',
        ];
    }
}
