<?php

namespace Tests\Feature;

use App\Models\QueueTicket;
use App\Models\User;
use App\Services\QueueEngine;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_pending_ticket_in_waiting_payment_status(): void
    {
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData([
            'payment_status' => QueueTicket::PAYMENT_PENDING,
            'department' => QueueTicket::DEPARTMENT_INSURANCE,
        ]));

        $this->assertSame('I001', $ticket->queue_number);
        $this->assertSame(QueueTicket::STATUS_WAITING_PAYMENT, $ticket->status);
    }

    public function test_it_creates_paid_ticket_in_ready_status(): void
    {
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData([
            'payment_status' => QueueTicket::PAYMENT_PAID,
        ]));

        $this->assertSame(QueueTicket::STATUS_READY, $ticket->status);
    }

    public function test_only_ready_tickets_can_be_called(): void
    {
        $engine = app(QueueEngine::class);
        $waitingPayment = $engine->createTicket($this->ticketData([
            'payment_status' => QueueTicket::PAYMENT_PENDING,
        ]));

        $this->assertNull($engine->callNext(QueueTicket::DEPARTMENT_INTERNAL));

        $engine->activatePayment($waitingPayment);
        $called = $engine->callNext(QueueTicket::DEPARTMENT_INTERNAL);

        $this->assertSame($waitingPayment->id, $called?->id);
        $this->assertSame(QueueTicket::STATUS_CALLING, $called?->status);
    }

    public function test_emergency_priority_level_five_is_called_before_other_priorities(): void
    {
        $engine = app(QueueEngine::class);
        $engine->createTicket($this->ticketData([
            'patient_name' => 'Bệnh nhân ưu tiên cao',
            'priority_level' => 4,
            'priority_reason' => 'severe_symptoms',
        ]));
        $emergency = $engine->createTicket($this->ticketData([
            'patient_name' => 'Bệnh nhân cấp cứu',
            'service_type' => QueueTicket::SERVICE_CAP_CUU,
            'department' => QueueTicket::DEPARTMENT_INTERNAL,
            'payment_status' => QueueTicket::PAYMENT_EXEMPTED,
            'priority_level' => 5,
            'priority_reason' => 'emergency',
        ]));

        $called = $engine->callNext(QueueTicket::DEPARTMENT_INTERNAL);

        $this->assertSame($emergency->id, $called?->id);
    }

    public function test_higher_priority_is_called_before_lower_priority(): void
    {
        $engine = app(QueueEngine::class);
        $engine->createTicket($this->ticketData(['priority_level' => 0]));
        $priority = $engine->createTicket($this->ticketData([
            'patient_name' => 'Người bệnh ưu tiên',
            'priority_level' => 3,
            'priority_reason' => 'elderly_75',
        ]));

        $called = $engine->callNext(QueueTicket::DEPARTMENT_INTERNAL);

        $this->assertSame($priority->id, $called?->id);
    }

    public function test_older_ticket_is_called_first_when_priority_is_the_same(): void
    {
        $engine = app(QueueEngine::class);
        $older = $engine->createTicket($this->ticketData(['patient_name' => 'Bệnh nhân đến trước']));
        $older->forceFill(['created_at' => now()->subMinutes(10)])->save();
        $engine->createTicket($this->ticketData(['patient_name' => 'Bệnh nhân đến sau']));

        $called = $engine->callNext(QueueTicket::DEPARTMENT_INTERNAL);

        $this->assertSame($older->id, $called?->id);
    }

    public function test_missed_ticket_can_be_recalled_manually(): void
    {
        $engine = app(QueueEngine::class);
        $ticket = $engine->createTicket($this->ticketData());
        $called = $engine->callNext(QueueTicket::DEPARTMENT_INTERNAL);
        $missed = $engine->markMissed($called->id);
        $recalled = $engine->recall($missed->id);

        $this->assertSame(QueueTicket::STATUS_MISSED, $missed->status);
        $this->assertSame(QueueTicket::STATUS_CALLING, $recalled->status);
    }

    public function test_serving_ticket_can_be_completed(): void
    {
        $engine = app(QueueEngine::class);
        $ticket = $engine->createTicket($this->ticketData());
        $called = $engine->callNext(QueueTicket::DEPARTMENT_INTERNAL);
        $serving = $engine->markServing($called->id);
        $completed = $engine->complete($serving->id);

        $this->assertSame(QueueTicket::STATUS_COMPLETED, $completed->status);
        $this->assertNotNull($completed->completed_at);
    }

    public function test_completed_ticket_cannot_be_modified(): void
    {
        $engine = app(QueueEngine::class);
        $ticket = $engine->createTicket($this->ticketData());
        $called = $engine->callNext(QueueTicket::DEPARTMENT_INTERNAL);
        $serving = $engine->markServing($called->id);
        $completed = $engine->complete($serving->id);

        $this->expectException(DomainException::class);
        $engine->cancel($completed->id);
    }

    public function test_cancelled_ticket_cannot_be_modified(): void
    {
        $engine = app(QueueEngine::class);
        $ticket = $engine->createTicket($this->ticketData());
        $cancelled = $engine->cancel($ticket->id);

        $this->expectException(DomainException::class);
        $engine->markServing($cancelled->id);
    }

    public function test_staff_routes_apply_queue_transitions(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_NURSE]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData());

        $this->actingAs($user)
            ->post(route('queue-tickets.call-next', ['department' => QueueTicket::DEPARTMENT_INTERNAL]))
            ->assertRedirect();

        $ticket->refresh();
        $this->assertSame(QueueTicket::STATUS_CALLING, $ticket->status);

        $this->actingAs($user)->patch(route('queue-tickets.serving', $ticket))->assertRedirect();
        $this->actingAs($user)->patch(route('queue-tickets.complete', $ticket))->assertRedirect();

        $this->assertSame(QueueTicket::STATUS_COMPLETED, $ticket->fresh()->status);
    }

    public function test_queue_form_rejects_priority_without_valid_reason(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_RECEPTIONIST]);

        $response = $this->actingAs($user)->post(route('queue-tickets.store'), $this->ticketData([
            'patient_name' => 'Bệnh nhân ưu tiên sai',
            'priority_level' => 3,
            'priority_reason' => 'normal',
            'notes' => 'Kiểm thử',
        ]));

        $response->assertSessionHasErrors('priority_reason');
        $this->assertDatabaseCount('queue_tickets', 0);
    }

    public function test_queue_form_requires_emergency_level_for_emergency_department(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_RECEPTIONIST]);

        $response = $this->actingAs($user)->post(route('queue-tickets.store'), $this->ticketData([
            'patient_name' => 'Bệnh nhân cấp cứu',
            'service_type' => QueueTicket::SERVICE_CAP_CUU,
            'department' => QueueTicket::DEPARTMENT_EMERGENCY,
            'payment_status' => QueueTicket::PAYMENT_EXEMPTED,
            'priority_reason' => 'emergency',
            'priority_level' => 4,
            'notes' => 'Khó thở, đau ngực',
        ]));

        $response->assertSessionHasErrors('priority_level');
        $this->assertDatabaseCount('queue_tickets', 0);
    }

    private function ticketData(array $overrides = []): array
    {
        return array_merge([
            'patient_name' => 'Nguyễn Văn A',
            'channel' => QueueTicket::CHANNEL_COUNTER,
            'service_type' => QueueTicket::SERVICE_DICH_VU,
            'department' => QueueTicket::DEPARTMENT_INTERNAL,
            'payment_status' => QueueTicket::PAYMENT_PAID,
            'priority_level' => 0,
            'priority_reason' => 'normal',
            'notes' => null,
        ], $overrides);
    }
}
