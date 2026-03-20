<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateInboxWorkflowRequest;
use App\Models\ContactMessage;
use App\Support\AdminAuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function index(): JsonResponse
    {
        $messages = ContactMessage::query()
            ->orderByDesc('created_at')
            ->get();

        $workflowSummary = collect(ContactMessage::WORKFLOW_STATUSES)
            ->mapWithKeys(fn (string $status): array => [$status => $messages->where('status', $status)->count()])
            ->all();

        return response()->json([
            'stats' => [
                'total' => $messages->count(),
                'unread' => $messages->where('is_read', false)->count(),
                'followUp' => $messages->whereIn('status', ['new', 'in_progress'])->count(),
                'byStatus' => $workflowSummary,
            ],
            'messages' => $messages->map(fn (ContactMessage $message): array => $this->formatMessage($message))->all(),
        ]);
    }

    public function markAsRead(Request $request, ContactMessage $contactMessage): JsonResponse
    {
        if (! $contactMessage->is_read) {
            $contactMessage->forceFill([
                'is_read' => true,
                'read_at' => now(),
            ])->save();

            AdminAuditLogger::record(
                $request->user(),
                'inbox',
                'read',
                sprintf('Marked inbox message from %s as read.', $contactMessage->name),
                [
                    'message_id' => $contactMessage->id,
                    'status' => $contactMessage->status,
                ],
            );
        }

        return response()->json([
            'message' => $this->formatMessage($contactMessage->fresh()),
        ]);
    }

    public function updateWorkflow(UpdateInboxWorkflowRequest $request, ContactMessage $contactMessage): JsonResponse
    {
        $data = $request->validated();
        $currentStatus = $contactMessage->status ?: 'new';

        if ($currentStatus !== $data['status']) {
            $contactMessage->forceFill([
                'status' => $data['status'],
                'status_changed_at' => now(),
                'is_read' => true,
                'read_at' => $contactMessage->read_at ?? now(),
            ])->save();

            AdminAuditLogger::record(
                $request->user(),
                'inbox',
                'workflow_updated',
                sprintf('Updated inbox workflow for %s from %s to %s.', $contactMessage->name, $currentStatus, $data['status']),
                [
                    'message_id' => $contactMessage->id,
                    'from' => $currentStatus,
                    'to' => $data['status'],
                    'category' => $contactMessage->category,
                ],
            );
        }

        return response()->json([
            'message' => $this->formatMessage($contactMessage->fresh()),
        ]);
    }

    private function formatMessage(ContactMessage $message): array
    {
        return [
            'id' => (string) $message->id,
            'name' => $message->name,
            'email' => $message->email,
            'whatsapp' => $message->whatsapp,
            'company' => $message->company,
            'category' => $message->category,
            'message' => $message->message,
            'status' => $message->status ?: 'new',
            'isRead' => $message->is_read,
            'submittedAt' => $message->created_at?->toIso8601String(),
            'readAt' => $message->read_at?->toIso8601String(),
            'statusChangedAt' => $message->status_changed_at?->toIso8601String(),
        ];
    }
}
