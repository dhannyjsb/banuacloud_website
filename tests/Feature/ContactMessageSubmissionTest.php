<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\CaseStudy;
use App\Models\ContactMessage;
use App\Models\FaqItem;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\VisitorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
            'category' => 'jaringan-gedung',
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
            'category' => 'jaringan-gedung',
            'status' => 'new',
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
            'category' => 'cloud',
            'message' => 'Mohon hubungi saya untuk penawaran cloud VPS.',
            'status' => 'new',
            'status_changed_at' => now(),
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/inbox');

        $response
            ->assertOk()
            ->assertJsonPath('stats.total', 1)
            ->assertJsonPath('stats.unread', 1)
            ->assertJsonPath('stats.byStatus.new', 1)
            ->assertJsonPath('messages.0.category', 'cloud')
            ->assertJsonPath('messages.0.name', 'Dhanny');
    }

    public function test_admin_can_update_inbox_workflow_and_create_audit_log(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $message = ContactMessage::query()->create([
            'name' => 'Dhanny',
            'email' => 'dhanny@example.com',
            'whatsapp' => '6281234567890',
            'company' => 'Banua Cloud Nusantara',
            'category' => 'backup',
            'message' => 'Butuh solusi backup terjadwal untuk server kantor.',
            'status' => 'new',
            'status_changed_at' => now(),
        ]);

        Sanctum::actingAs($admin);

        $response = $this->patchJson("/api/admin/inbox/{$message->id}/workflow", [
            'status' => 'contacted',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message.status', 'contacted')
            ->assertJsonPath('message.isRead', true);

        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
            'status' => 'contacted',
            'is_read' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'target' => 'inbox',
            'action' => 'workflow_updated',
        ]);
    }

    public function test_admin_dashboard_returns_real_counts(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        ContactMessage::query()->create([
            'name' => 'Inbox One',
            'email' => 'one@example.com',
            'whatsapp' => '6281111111111',
            'category' => 'cloud',
            'message' => 'Cloud need',
            'status' => 'new',
            'status_changed_at' => now(),
        ]);

        Service::query()->create([
            'name' => 'Cloud VPS',
            'slug' => 'cloud-vps',
            'description' => 'Managed VPS',
            'icon_key' => 'server',
            'enabled' => true,
            'sort_order' => 0,
        ]);

        Testimonial::query()->create([
            'name' => 'User A',
            'role' => 'CTO',
            'company' => 'ABC',
            'content' => 'Mantap',
            'sort_order' => 0,
        ]);

        FaqItem::query()->create([
            'question' => 'Apa saja layanannya?',
            'answer' => 'Cloud, hosting, backup, dan lainnya.',
            'sort_order' => 0,
        ]);

        CaseStudy::query()->create([
            'title' => 'Project A',
            'client_name' => 'Client A',
            'category' => 'cloud',
            'summary' => 'Summary',
            'challenge' => 'Challenge',
            'solution' => 'Solution',
            'outcome' => 'Outcome',
            'tags' => ['Cloud'],
            'is_featured' => true,
            'sort_order' => 0,
        ]);

        AuditLog::query()->create([
            'user_id' => $admin->id,
            'actor_name' => $admin->name,
            'actor_email' => $admin->email,
            'target' => 'content',
            'action' => 'updated',
            'summary' => 'Updated content.',
            'metadata' => [],
        ]);

        VisitorVisit::query()->create([
            'visitor_token' => 'visitor-token-1',
            'path' => '/',
            'route_name' => 'home',
            'page_title' => 'Home',
            'referrer_url' => null,
            'referrer_host' => null,
            'source' => 'Direct',
            'medium' => null,
            'utm_campaign' => null,
            'ip_address' => '8.8.8.8',
            'user_agent' => 'Mozilla/5.0',
            'country_code' => 'ID',
            'country_name' => 'Indonesia',
            'city_name' => 'Banjarmasin',
            'isp_name' => 'Biznet',
            'organization_name' => 'Biznet Networks',
            'autonomous_system_number' => 17451,
            'autonomous_system_organization' => 'PT. Supra Primatama Nusantara',
            'visited_at' => now(),
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('stats.totalMessages', 1)
            ->assertJsonPath('stats.activeServices', 1)
            ->assertJsonPath('stats.testimonials', 1)
            ->assertJsonPath('stats.faqs', 1)
            ->assertJsonPath('stats.caseStudies', 1)
            ->assertJsonPath('traffic.topIsps.0.label', 'Biznet')
            ->assertJsonPath('traffic.mostVisitedIps.0.ispName', 'Biznet')
            ->assertJsonPath('traffic.recentVisits.0.organizationName', 'Biznet Networks')
            ->assertJsonCount(1, 'recentAuditLogs');
    }

    public function test_admin_can_update_account_details(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Lama',
            'email' => 'admin-lama@example.com',
            'password' => 'password-sebelumnya',
            'role' => 'admin',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->putJson('/api/auth/account', [
            'name' => 'Admin Baru',
            'email' => 'admin-baru@example.com',
            'currentPassword' => 'password-sebelumnya',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Admin account updated successfully.')
            ->assertJsonPath('user.name', 'Admin Baru')
            ->assertJsonPath('user.email', 'admin-baru@example.com');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Baru',
            'email' => 'admin-baru@example.com',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'target' => 'account',
            'action' => 'updated',
        ]);
    }

    public function test_admin_account_update_requires_current_password(): void
    {
        $admin = User::factory()->create([
            'password' => 'password-sebelumnya',
            'role' => 'admin',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->putJson('/api/auth/account', [
            'name' => 'Nama Baru',
            'email' => 'nama.baru@example.com',
            'currentPassword' => 'password-salah',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'Current password is incorrect.');

        $this->assertTrue(Hash::check('password-sebelumnya', $admin->fresh()->password));
    }
}
