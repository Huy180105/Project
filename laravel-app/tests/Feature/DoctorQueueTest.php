<?php

namespace Tests\Feature;

use App\Models\QueueTicket;
use App\Models\QueueTicketEvent;
use App\Models\User;
use App\Services\QueueEngine;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_call_next_ready_ticket(): void
    {
        $doctor = User::factory()->create(['role' => User::ROLE_DOCTOR]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData());

        $this->actingAs($doctor)
            ->post(route('doctor.queue.call-next'), ['department' => QueueTicket::DEPARTMENT_INTERNAL])
            ->assertRedirect();

        $this->assertSame(QueueTicket::STATUS_CALLING, $ticket->fresh()->status);
    }

    public function test_emergency_ticket_is_called_before_normal_ticket(): void
    {
        $doctor = User::factory()->create(['role' => User::ROLE_DOCTOR]);
        app(QueueEngine::class)->createTicket($this->ticketData());
        $emergency = app(QueueEngine::class)->createTicket($this->ticketData([
            'patient_name' => 'Bệnh nhân cấp cứu',
            'service_type' => QueueTicket::SERVICE_CAP_CUU,
            'department' => QueueTicket::DEPARTMENT_INTERNAL,
            'payment_status' => QueueTicket::PAYMENT_EXEMPTED,
            'priority_level' => 5,
            'priority_reason' => 'emergency',
        ]));

        $this->actingAs($doctor)
            ->post(route('doctor.queue.call-next'), ['department' => QueueTicket::DEPARTMENT_INTERNAL]);

        $this->assertSame(QueueTicket::STATUS_CALLING, $emergency->fresh()->status);
    }

    public function test_waiting_payment_ticket_is_not_called(): void
    {
        $doctor = User::factory()->create(['role' => User::ROLE_DOCTOR]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData([
            'payment_status' => QueueTicket::PAYMENT_PENDING,
        ]));

        $this->actingAs($doctor)
            ->post(route('doctor.queue.call-next'), ['department' => QueueTicket::DEPARTMENT_INTERNAL]);

        $this->assertSame(QueueTicket::STATUS_WAITING_PAYMENT, $ticket->fresh()->status);
    }

    public function test_doctor_can_mark_calling_ticket_as_serving(): void
    {
        $doctor = User::factory()->create(['role' => User::ROLE_DOCTOR]);
        $ticket = $this->calledTicket();

        $this->actingAs($doctor)
            ->patch(route('doctor.queue.serving', $ticket))
            ->assertRedirect();

        $this->assertSame(QueueTicket::STATUS_SERVING, $ticket->fresh()->status);
    }

    public function test_doctor_can_complete_serving_ticket(): void
    {
        $doctor = User::factory()->create(['role' => User::ROLE_DOCTOR]);
        $ticket = $this->calledTicket();
        app(QueueEngine::class)->markServing($ticket->id);

        $this->actingAs($doctor)
            ->patch(route('doctor.queue.complete', $ticket))
            ->assertRedirect();

        $this->assertSame(QueueTicket::STATUS_COMPLETED, $ticket->fresh()->status);
    }

    public function test_doctor_cannot_complete_ready_ticket(): void
    {
        $doctor = User::factory()->create(['role' => User::ROLE_DOCTOR]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData());

        $this->actingAs($doctor)
            ->patch(route('doctor.queue.complete', $ticket))
            ->assertSessionHasErrors('queue');

        $this->assertSame(QueueTicket::STATUS_READY, $ticket->fresh()->status);
    }

    public function test_missed_ticket_can_be_recalled(): void
    {
        $doctor = User::factory()->create(['role' => User::ROLE_DOCTOR]);
        $ticket = $this->calledTicket();
        app(QueueEngine::class)->markMissed($ticket->id);

        $this->actingAs($doctor)
            ->patch(route('doctor.queue.recall', $ticket))
            ->assertRedirect();

        $this->assertSame(QueueTicket::STATUS_CALLING, $ticket->fresh()->status);
    }

    public function test_queue_actions_are_audited(): void
    {
        $doctor = User::factory()->create(['role' => User::ROLE_DOCTOR]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData());

        $this->actingAs($doctor)
            ->post(route('doctor.queue.call-next'), ['department' => QueueTicket::DEPARTMENT_INTERNAL]);

        $event = QueueTicketEvent::where('action', 'called')->first();

        $this->assertSame($ticket->id, $event->queue_ticket_id);
        $this->assertSame('called', $event->action);
        $this->assertSame(QueueTicket::STATUS_READY, $event->old_status);
        $this->assertSame(QueueTicket::STATUS_CALLING, $event->new_status);
        $this->assertSame($doctor->id, $event->performed_by);
    }

    public function test_department_cannot_have_two_active_tickets(): void
    {
        $engine = app(QueueEngine::class);
        $engine->createTicket($this->ticketData());
        $engine->createTicket($this->ticketData(['patient_name' => 'Bệnh nhân thứ hai']));

        $engine->callNext(QueueTicket::DEPARTMENT_INTERNAL);

        $this->expectException(DomainException::class);
        $engine->callNext(QueueTicket::DEPARTMENT_INTERNAL);
    }

    private function calledTicket(): QueueTicket
    {
        $engine = app(QueueEngine::class);
        $engine->createTicket($this->ticketData());

        return $engine->callNext(QueueTicket::DEPARTMENT_INTERNAL);
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
