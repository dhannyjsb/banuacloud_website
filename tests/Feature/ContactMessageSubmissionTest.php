<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ContactMessageSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_message_can_be_submitted(): void
    {
        $response = $this->postJson('/api/site/contact-messages', [
            'name' => 'Dhanny',
            'email' => 'dhanny@example.com',
            'whatsapp' => '6281234567890',
            'company' => 'Banua Cloud Nusantara',
            'message' => 'Saya ingin konsultasi terkait pembangunan jaringan kantor.',
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'message' => 'Pesan Anda sudah kami terima. Tim kami akan segera menghubungi Anda.',
            ]);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Dhanny',
            'email' => 'dhanny@example.com',
            'whatsapp' => '6281234567890',
            'company' => 'Banua Cloud Nusantara',
        ]);
    }

    public function test_admin_can_list_contact_messages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        ContactMessage::query()->create([
            'name' => 'Dhanny',
            'email' => 'dhanny@example.com',
            'whatsapp' => '6281234567890',
            'company' => null,
            'message' => 'Mohon hubungi saya untuk penawaran cloud VPS.',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/inbox');

        $response
            ->assertOk()
            ->assertJsonPath('stats.total', 1)
            ->assertJsonPath('stats.unread', 1)
            ->assertJsonPath('messages.0.name', 'Dhanny');
    }
}
