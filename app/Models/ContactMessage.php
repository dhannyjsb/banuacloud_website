<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'email',
    'whatsapp',
    'company',
    'category',
    'message',
    'status',
    'status_changed_at',
    'is_read',
    'read_at',
])]
class ContactMessage extends Model
{
    public const CATEGORY_OPTIONS = [
        'cloud',
        'hosting',
        'jaringan-gedung',
        'backup',
        'aplikasi',
        'konsultasi',
    ];

    public const WORKFLOW_STATUSES = [
        'new',
        'in_progress',
        'contacted',
        'resolved',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'status_changed_at' => 'datetime',
        ];
    }
}
