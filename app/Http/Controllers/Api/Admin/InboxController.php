<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

class InboxController extends Controller
{
    public function index(): JsonResponse
    {
        $messages = ContactMessage::query()
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'stats' => [
                'total' => $messages->count(),
                'unread' => $messages->where('is_read', false)->count(),
            ],
            'messages' => $messages->map(fn(ContactMessage $message): array => $this->formatMessage($message))->all(),
        ]);
    }

    public function markAsRead(ContactMessage $contactMessage): JsonResponse
    {
        if (! $contactMessage->is_read) {
            $contactMessage->forceFill([
                'is_read' => true,
                'read_at' => now(),
            ])->save();
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
            'message' => $message->message,
            'isRead' => $message->is_read,
            'submittedAt' => $message->created_at?->toIso8601String(),
            'readAt' => $message->read_at?->toIso8601String(),
        ];
    }
}
