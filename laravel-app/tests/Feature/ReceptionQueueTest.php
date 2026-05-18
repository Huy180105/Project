<?php

namespace Tests\Feature;

use App\Models\QueueTicket;
use App\Models\QueueTicketEvent;
use App\Models\User;
use App\Services\QueueEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceptionQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_receptionist_can_create_waiting_payment_ticket(): void
    {
        $receptionist = User::factory()->create(['role' => User::ROLE_RECEPTIONIST]);

        $this->actingAs($receptionist)
            ->post(route('reception.queue.store'), $this->ticketData(['payment_status' => QueueTicket::PAYMENT_PENDING]))
            ->assertRedirect();

        $this->assertDatabaseHas('queue_tickets', ['status' => QueueTicket::STATUS_WAITING_PAYMENT]);
    }

    public function test_confirmed_his_ticket_becomes_ready(): void
    {
        $receptionist = User::factory()->create(['role' => User::ROLE_RECEPTIONIST]);

        $this->actingAs($receptionist)
            ->post(route('reception.queue.store'), $this->ticketData(['payment_status' => QueueTicket::PAYMENT_PAID]))
            ->assertRedirect();

        $this->assertDatabaseHas('queue_tickets', ['status' => QueueTicket::STATUS_READY]);
    }

    public function test_emergency_cannot_be_waiting_payment(): void
    {
        $receptionist = User::factory()->create(['role' => User::ROLE_RECEPTIONIST]);

        $this->actingAs($receptionist)
            ->post(route('reception.queue.store'), $this->ticketData([
                'service_type' => QueueTicket::SERVICE_CAP_CUU,
                'department' => QueueTicket::DEPARTMENT_EMERGENCY,
                'payment_status' => QueueTicket::PAYMENT_PENDING,
                'priority_reason' => 'emergency',
                'priority_level' => 5,
                'notes' => 'Khó thở',
            ]))
            ->assertSessionHasErrors('payment_status');
    }

    public function test_activation_changes_waiting_payment_to_ready(): void
    {
        $receptionist = User::factory()->create(['role' => User::ROLE_RECEPTIONIST]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData(['payment_status' => QueueTicket::PAYMENT_PENDING]));

        $this->actingAs($receptionist)->patch(route('reception.queue.activate', $ticket))->assertRedirect();

        $this->assertSame(QueueTicket::STATUS_READY, $ticket->fresh()->status);
    }

    public function test_cannot_activate_completed_or_cancelled_ticket(): void
    {
        $receptionist = User::factory()->create(['role' => User::ROLE_RECEPTIONIST]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData([
            'payment_status' => QueueTicket::PAYMENT_PAID,
        ]));
        $called = app(QueueEngine::class)->callNext(QueueTicket::DEPARTMENT_INTERNAL);
        $serving = app(QueueEngine::class)->markServing($called->id);
        $completed = app(QueueEngine::class)->complete($serving->id);

        $this->actingAs($receptionist)->patch(route('reception.queue.activate', $completed))->assertSessionHasErrors('queue');

        $cancelledTicket = app(QueueEngine::class)->createTicket($this->ticketData([
            'patient_name' => 'Bệnh nhân hủy',
            'payment_status' => QueueTicket::PAYMENT_PENDING,
        ]));
        $cancelled = app(QueueEngine::class)->cancel($cancelledTicket->id);
        $this->actingAs($receptionist)->patch(route('reception.queue.activate', $cancelled))->assertSessionHasErrors('queue');
    }

    public function test_cancel_creates_audit_log(): void
    {
        $receptionist = User::factory()->create(['role' => User::ROLE_RECEPTIONIST]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData());

        $this->actingAs($receptionist)->patch(route('reception.queue.cancel', $ticket))->assertRedirect();

        $event = QueueTicketEvent::where('action', 'cancelled')->first();
        $this->assertSame($ticket->id, $event->queue_ticket_id);
        $this->assertSame($receptionist->id, $event->performed_by);
    }

    public function test_invalid_priority_reason_is_rejected(): void
    {
        $receptionist = User::factory()->create(['role' => User::ROLE_RECEPTIONIST]);

        $this->actingAs($receptionist)
            ->post(route('reception.queue.store'), $this->ticketData([
                'priority_reason' => 'normal',
                'priority_level' => 3,
                'notes' => 'Không hợp lệ',
            ]))
            ->assertSessionHasErrors('priority_reason');
    }

    private function ticketData(array $overrides = []): array
    {
        return array_merge([
            'patient_name' => 'Nguyễn Văn A',
            'channel' => QueueTicket::CHANNEL_COUNTER,
            'service_type' => QueueTicket::SERVICE_DICH_VU,
            'department' => QueueTicket::DEPARTMENT_INTERNAL,
            'payment_status' => QueueTicket::PAYMENT_PENDING,
            'priority_level' => 0,
            'priority_reason' => 'normal',
            'notes' => null,
        ], $overrides);
    }
}
