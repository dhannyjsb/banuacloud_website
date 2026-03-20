<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminAuditLogger
{
    public static function record(
        ?Authenticatable $actor,
        string $target,
        string $action,
        string $summary,
        array $metadata = [],
    ): void {
        $user = $actor instanceof User ? $actor : null;

        AuditLog::query()->create([
            'user_id' => $user?->id,
            'actor_name' => $user?->name ?? 'System',
            'actor_email' => $user?->email ?? 'system@localhost',
            'action' => $action,
            'target' => $target,
            'summary' => $summary,
            'metadata' => $metadata,
        ]);
    }
}
