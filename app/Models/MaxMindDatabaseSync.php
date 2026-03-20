<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'edition_id',
    'database_path',
    'status',
    'file_exists',
    'message',
    'metadata',
    'checked_at',
    'downloaded_at',
])]
class MaxMindDatabaseSync extends Model
{
    protected function casts(): array
    {
        return [
            'file_exists' => 'boolean',
            'metadata' => 'array',
            'checked_at' => 'datetime',
            'downloaded_at' => 'datetime',
        ];
    }
}
