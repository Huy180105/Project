<?php

namespace Tests\Feature;

use App\Models\NotificationLog;
use App\Models\QueueTicket;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\QueueEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NotificationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_calling_ticket_creates_calling_notification_log(): void
    {
        config(['services.n8n.notification_webhook_url' => null]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData([
            'payment_status' => QueueTicket::PAYMENT_PAID,
        ]));

        app(QueueEngine::class)->callNext(QueueTicket::DEPARTMENT_INTERNAL);

        $this->assertDatabaseHas('notification_logs', [
            'queue_ticket_id' => $ticket->id,
            'type' => NotificationLog::TYPE_CALLING,
            'status' => NotificationLog::STATUS_PENDING,
        ]);
    }

    public function test_near_turn_creates_notification_log(): void
    {
        $patient = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData([
            'payment_status' => QueueTicket::PAYMENT_PAID,
        ]), $patient->id);

        $this->actingAs($patient, 'sanctum')
            ->getJson("/api/queue-status/{$ticket->id}")
            ->assertOk();

        $this->assertDatabaseHas('notification_logs', [
            'queue_ticket_id' => $ticket->id,
            'type' => NotificationLog::TYPE_NEAR_TURN,
        ]);
    }

    public function test_duplicate_near_turn_notification_is_not_created(): void
    {
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData([
            'payment_status' => QueueTicket::PAYMENT_PAID,
        ]));
        $service = app(NotificationService::class);

        $service->notifyNearTurn($ticket);
        $service->notifyNearTurn($ticket);

        $this->assertSame(1, NotificationLog::where('queue_ticket_id', $ticket->id)
            ->where('type', NotificationLog::TYPE_NEAR_TURN)
            ->count());
    }

    public function test_missing_n8n_webhook_does_not_break_queue_flow(): void
    {
        config(['services.n8n.notification_webhook_url' => null]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData([
            'payment_status' => QueueTicket::PAYMENT_PAID,
        ]));

        $called = app(QueueEngine::class)->callNext(QueueTicket::DEPARTMENT_INTERNAL);

        $this->assertSame($ticket->id, $called?->id);
        $this->assertDatabaseHas('notification_logs', [
            'queue_ticket_id' => $ticket->id,
            'status' => NotificationLog::STATUS_PENDING,
        ]);
    }

    public function test_configured_n8n_webhook_marks_notification_as_sent(): void
    {
        config(['services.n8n.notification_webhook_url' => 'https://n8n.example.test/webhook']);
        Http::fake([
            'https://n8n.example.test/webhook' => Http::response([], 200),
        ]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData([
            'payment_status' => QueueTicket::PAYMENT_PAID,
        ]));

        app(QueueEngine::class)->callNext(QueueTicket::DEPARTMENT_INTERNAL);

        $this->assertDatabaseHas('notification_logs', [
            'queue_ticket_id' => $ticket->id,
            'status' => NotificationLog::STATUS_SENT,
        ]);
    }

    public function test_notification_log_page_loads(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)
            ->get('/notifications')
            ->assertOk()
            ->assertSee('Nhật ký thông báo');
    }

    private function ticketData(array $overrides = []): array
    {
        return array_merge([
            'patient_name' => 'Bệnh nhân thông báo',
            'patient_phone' => '0901234567',
            'channel' => QueueTicket::CHANNEL_MOBILE,
            'service_type' => QueueTicket::SERVICE_DICH_VU,
            'department' => QueueTicket::DEPARTMENT_INTERNAL,
            'payment_status' => QueueTicket::PAYMENT_PENDING,
            'priority_level' => 0,
            'priority_reason' => 'normal',
            'notes' => null,
        ], $overrides);
    }
}
