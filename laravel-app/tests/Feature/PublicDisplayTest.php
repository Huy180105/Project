<?php

namespace Tests\Feature;

use App\Models\QueueTicket;
use App\Services\QueueEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_display_only_shows_department_tickets(): void
    {
        $internal = app(QueueEngine::class)->createTicket($this->ticketData([
            'patient_name' => 'Bệnh nhân nội tổng quát',
        ]));
        app(QueueEngine::class)->createTicket($this->ticketData([
            'patient_name' => 'Bệnh nhân tim mạch',
            'department' => QueueTicket::DEPARTMENT_CARDIOLOGY,
        ]));

        $this->get(route('display.department', QueueTicket::DEPARTMENT_INTERNAL))
            ->assertOk()
            ->assertSee($internal->displayNumber())
            ->assertSee('Bệnh nhân nội tổng quát')
            ->assertDontSee('Bệnh nhân tim mạch');
    }

    public function test_display_excludes_completed_tickets(): void
    {
        $engine = app(QueueEngine::class);
        $ticket = $engine->createTicket($this->ticketData());
        $called = $engine->callNext(QueueTicket::DEPARTMENT_INTERNAL);
        $serving = $engine->markServing($called->id);
        $completed = $engine->complete($serving->id);

        $this->get(route('display.department', QueueTicket::DEPARTMENT_INTERNAL))
            ->assertOk()
            ->assertDontSee($completed->displayNumber());
    }

    public function test_next_queue_is_ordered_by_priority_then_age(): void
    {
        $engine = app(QueueEngine::class);
        $older = $engine->createTicket($this->ticketData(['patient_name' => 'Bệnh nhân cũ']));
        $older->forceFill(['created_at' => now()->subMinutes(10)])->save();
        $priority = $engine->createTicket($this->ticketData([
            'patient_name' => 'Bệnh nhân ưu tiên',
            'priority_level' => 3,
            'priority_reason' => 'elderly_75',
        ]));

        $response = $this->get(route('display.department', QueueTicket::DEPARTMENT_INTERNAL));

        $response->assertSeeInOrder([
            $priority->displayNumber(),
            $older->displayNumber(),
        ]);
    }

    public function test_calling_ticket_is_shown_before_ready_queue(): void
    {
        $engine = app(QueueEngine::class);
        $calling = $engine->createTicket($this->ticketData(['patient_name' => 'Bệnh nhân đang gọi']));
        $engine->createTicket($this->ticketData(['patient_name' => 'Bệnh nhân kế tiếp']));
        $engine->callNext(QueueTicket::DEPARTMENT_INTERNAL);

        $this->get(route('display.department', QueueTicket::DEPARTMENT_INTERNAL))
            ->assertOk()
            ->assertSeeInOrder([
                'Đang gọi',
                $calling->displayNumber(),
                'Tiếp theo',
            ]);
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
