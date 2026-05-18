<?php

namespace Tests\Feature;

use App\Models\QueueTicket;
use App\Models\QueueTicketEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KioskModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_kiosk_page_shows_departments(): void
    {
        $this->get(route('kiosk.index'))
            ->assertOk()
            ->assertSee(QueueTicket::DEPARTMENT_INTERNAL)
            ->assertSee(QueueTicket::DEPARTMENT_CARDIOLOGY);
    }

    public function test_kiosk_can_create_normal_waiting_payment_ticket(): void
    {
        $response = $this->post(route('kiosk.tickets.store'), $this->payload());

        $ticket = QueueTicket::firstOrFail();

        $response->assertRedirect(route('kiosk.tickets.show', $ticket));
        $this->assertSame(QueueTicket::STATUS_WAITING_PAYMENT, $ticket->status);
        $this->assertSame(QueueTicket::PAYMENT_PENDING, $ticket->payment_status);
        $this->assertDatabaseHas('queue_ticket_events', [
            'queue_ticket_id' => $ticket->id,
            'action' => 'created',
        ]);
    }

    public function test_kiosk_emergency_ticket_becomes_ready(): void
    {
        $this->post(route('kiosk.tickets.store'), $this->payload([
            'department' => QueueTicket::DEPARTMENT_EMERGENCY,
            'priority_reason' => 'emergency',
        ]));

        $ticket = QueueTicket::firstOrFail();

        $this->assertSame(QueueTicket::STATUS_READY, $ticket->status);
        $this->assertSame(QueueTicket::PAYMENT_EXEMPTED, $ticket->payment_status);
        $this->assertSame(5, $ticket->priority_level);
    }

    public function test_kiosk_channel_is_forced_to_kiosk(): void
    {
        $this->post(route('kiosk.tickets.store'), $this->payload());

        $this->assertSame(QueueTicket::CHANNEL_KIOSK, QueueTicket::firstOrFail()->channel);
    }

    public function test_priority_level_is_derived_from_reason(): void
    {
        $this->post(route('kiosk.tickets.store'), $this->payload([
            'priority_reason' => 'elderly_75',
        ]));

        $this->assertSame(3, QueueTicket::firstOrFail()->priority_level);
    }

    public function test_kiosk_ticket_result_page_displays_queue_number(): void
    {
        $this->post(route('kiosk.tickets.store'), $this->payload());
        $ticket = QueueTicket::firstOrFail();

        $this->get(route('kiosk.tickets.show', $ticket))
            ->assertOk()
            ->assertSee($ticket->displayNumber());
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'patient_name' => 'Nguyễn Văn An',
            'patient_phone' => '0901234567',
            'department' => QueueTicket::DEPARTMENT_INTERNAL,
            'priority_reason' => 'normal',
        ], $overrides);
    }
}
