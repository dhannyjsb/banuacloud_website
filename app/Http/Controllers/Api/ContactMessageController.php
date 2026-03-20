<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

class ContactMessageController extends Controller
{
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        $data = $request->validated();

        ContactMessage::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'whatsapp' => $data['whatsapp'],
            'company' => $data['company'] ?? null,
            'category' => $data['category'],
            'message' => $data['message'],
            'status' => 'new',
            'status_changed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Pesan Anda sudah kami terima. Tim kami akan segera menghubungi Anda.',
        ], 201);
    }
}
