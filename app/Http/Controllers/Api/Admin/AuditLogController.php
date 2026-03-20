<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;

class AuditLogController extends Controller
{
    public function index(): JsonResponse
    {
        $logs = AuditLog::query()
            ->latest()
            ->limit(100)
            ->get();

        return response()->json([
            'stats' => [
                'total' => AuditLog::query()->count(),
                'today' => AuditLog::query()->whereDate('created_at', now()->toDateString())->count(),
            ],
            'logs' => $logs->map(fn (AuditLog $log): array => [
                'id' => (string) $log->id,
                'actorName' => $log->actor_name,
                'actorEmail' => $log->actor_email,
                'action' => $log->action,
                'target' => $log->target,
                'summary' => $log->summary,
                'metadata' => $log->metadata ?? [],
                'createdAt' => $log->created_at?->toIso8601String(),
            ])->values()->all(),
        ]);
    }
}
