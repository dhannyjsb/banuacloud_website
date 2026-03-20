<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CaseStudy;
use App\Models\ContactMessage;
use App\Models\FaqItem;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function show(): JsonResponse
    {
        $messages = ContactMessage::query()
            ->orderByDesc('created_at')
            ->get();

        $workflow = collect(ContactMessage::WORKFLOW_STATUSES)
            ->map(fn (string $status): array => [
                'key' => $status,
                'count' => $messages->where('status', $status)->count(),
            ])
            ->values()
            ->all();

        $categories = collect(ContactMessage::CATEGORY_OPTIONS)
            ->map(fn (string $category): array => [
                'key' => $category,
                'count' => $messages->where('category', $category)->count(),
            ])
            ->values()
            ->all();

        $settings = SiteSetting::query()->first();

        return response()->json([
            'stats' => [
                'totalMessages' => $messages->count(),
                'unreadMessages' => $messages->where('is_read', false)->count(),
                'followUpMessages' => $messages->whereIn('status', ['new', 'in_progress'])->count(),
                'activeServices' => Service::query()->where('enabled', true)->count(),
                'testimonials' => Testimonial::query()->count(),
                'faqs' => FaqItem::query()->count(),
                'caseStudies' => CaseStudy::query()->count(),
                'maintenanceMode' => (bool) $settings?->maintenance_mode,
            ],
            'workflow' => $workflow,
            'categories' => $categories,
            'recentMessages' => $messages
                ->take(6)
                ->map(fn (ContactMessage $message): array => [
                    'id' => (string) $message->id,
                    'name' => $message->name,
                    'company' => $message->company,
                    'category' => $message->category,
                    'status' => $message->status ?: 'new',
                    'isRead' => $message->is_read,
                    'submittedAt' => $message->created_at?->toIso8601String(),
                ])
                ->values()
                ->all(),
            'recentAuditLogs' => AuditLog::query()
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn (AuditLog $log): array => [
                    'id' => (string) $log->id,
                    'actorName' => $log->actor_name,
                    'actorEmail' => $log->actor_email,
                    'action' => $log->action,
                    'target' => $log->target,
                    'summary' => $log->summary,
                    'createdAt' => $log->created_at?->toIso8601String(),
                ])
                ->values()
                ->all(),
        ]);
    }
}
