<?php

namespace Tests\Feature;

use App\Models\QueueTicket;
use App\Models\User;
use App\Services\QueueEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_register(): void
    {
        $this->postJson('/api/register', $this->registerPayload())
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.role', User::ROLE_PATIENT)
            ->assertJsonPath('data.user.patient_profile.phone', '0901234567');
    }

    public function test_register_creates_patient_profile(): void
    {
        $this->postJson('/api/register', $this->registerPayload())->assertCreated();

        $this->assertDatabaseHas('patient_profiles', [
            'phone' => '0901234567',
            'insurance_number' => 'HS123456789',
            'citizen_id' => '001203000000',
        ]);
    }

    public function test_patient_can_login(): void
    {
        $patient = User::factory()->create([
            'email' => 'patient@example.com',
            'password' => 'password123',
            'role' => User::ROLE_PATIENT,
        ]);

        $this->postJson('/api/login', [
            'email' => $patient->email,
            'password' => 'password123',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token', 'user']]);
    }

    public function test_patient_can_create_ticket(): void
    {
        $patient = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $patient->patientProfile()->create(['phone' => '0901234567']);

        $this->actingAs($patient, 'sanctum')
            ->postJson('/api/tickets', [
                'department_id' => 1,
                'priority_reason' => 'none',
            ])
            ->assertCreated()
            ->assertJsonPath('data.patient_name', $patient->name)
            ->assertJsonPath('data.patient_phone', '0901234567')
            ->assertJsonPath('data.department.id', 1)
            ->assertJsonPath('data.status', QueueTicket::STATUS_WAITING_PAYMENT);

        $this->assertDatabaseHas('queue_tickets', [
            'user_id' => $patient->id,
            'channel' => QueueTicket::CHANNEL_MOBILE,
        ]);
    }

    public function test_patient_cannot_access_another_patient_ticket(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $other = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData(), $owner->id);

        $this->actingAs($other, 'sanctum')
            ->getJson("/api/queue-status/{$ticket->id}")
            ->assertForbidden();
    }

    public function test_patient_can_get_own_qr_payload_without_sensitive_profile_data(): void
    {
        $patient = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $patient->patientProfile()->create([
            'phone' => '0901234567',
            'insurance_number' => 'HS123456789',
            'citizen_id' => '001203000000',
            'medical_history' => 'Tăng huyết áp',
            'allergies' => 'Penicillin',
        ]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData(), $patient->id);

        $response = $this->actingAs($patient, 'sanctum')
            ->getJson("/api/tickets/{$ticket->id}/qr")
            ->assertOk()
            ->assertJsonPath('data.ticket_id', $ticket->id)
            ->assertJsonPath('data.queue_number', $ticket->displayNumber())
            ->assertJsonPath('data.department_id', 1);

        $payload = json_decode($response->json('data.qr_payload'), true);

        $this->assertSame($ticket->id, $payload['ticket_id']);
        $this->assertSame($ticket->displayNumber(), $payload['queue_number']);
        $this->assertArrayNotHasKey('phone', $payload);
        $this->assertArrayNotHasKey('citizen_id', $payload);
        $this->assertArrayNotHasKey('insurance_number', $payload);
        $this->assertArrayNotHasKey('medical_history', $payload);
        $this->assertArrayNotHasKey('allergies', $payload);
    }

    public function test_patient_cannot_get_another_patient_qr_payload(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $other = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData(), $owner->id);

        $this->actingAs($other, 'sanctum')
            ->getJson("/api/tickets/{$ticket->id}/qr")
            ->assertForbidden();
    }

    public function test_department_list_is_available(): void
    {
        $this->getJson('/api/departments')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment([
                'id' => 1,
                'name' => QueueTicket::DEPARTMENT_INTERNAL,
                'room_number' => QueueTicket::roomForService(QueueTicket::SERVICE_DICH_VU, QueueTicket::DEPARTMENT_INTERNAL),
                'current_number' => null,
                'average_time_per_patient' => 4,
            ]);
    }

    public function test_queue_status_returns_patient_ticket(): void
    {
        $patient = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $calling = app(QueueEngine::class)->createTicket($this->ticketData([
            'patient_name' => 'Người đang được gọi',
            'payment_status' => QueueTicket::PAYMENT_PAID,
        ]));
        app(QueueEngine::class)->callNext(QueueTicket::DEPARTMENT_INTERNAL);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData([
            'payment_status' => QueueTicket::PAYMENT_PAID,
        ]), $patient->id);

        $this->actingAs($patient, 'sanctum')
            ->getJson("/api/queue-status/{$ticket->id}")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.queue_number', $ticket->displayNumber())
            ->assertJsonPath('data.queue_position', 1)
            ->assertJsonPath('data.remaining_before_me', 0)
            ->assertJsonPath('data.current_calling_number', $calling->displayNumber());
    }

    public function test_my_ticket_returns_latest_active_ticket_for_authenticated_patient(): void
    {
        $patient = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $older = app(QueueEngine::class)->createTicket($this->ticketData(), $patient->id);
        $latest = app(QueueEngine::class)->createTicket($this->ticketData([
            'department' => QueueTicket::DEPARTMENT_CARDIOLOGY,
        ]), $patient->id);

        $this->actingAs($patient, 'sanctum')
            ->getJson('/api/my-ticket')
            ->assertOk()
            ->assertJsonPath('data.id', $latest->id)
            ->assertJsonMissing(['id' => $older->id]);
    }

    public function test_completed_ticket_is_not_returned_as_active_my_ticket(): void
    {
        $patient = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $ticket = app(QueueEngine::class)->createTicket($this->ticketData([
            'payment_status' => QueueTicket::PAYMENT_PAID,
        ]), $patient->id);
        app(QueueEngine::class)->callNext(QueueTicket::DEPARTMENT_INTERNAL);
        app(QueueEngine::class)->markServing($ticket->id);
        app(QueueEngine::class)->complete($ticket->id);

        $this->actingAs($patient, 'sanctum')
            ->getJson('/api/my-ticket')
            ->assertOk()
            ->assertJsonPath('data', null);
    }

    public function test_profile_returns_patient_profile(): void
    {
        $patient = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $patient->patientProfile()->create([
            'phone' => '0901234567',
            'medical_history' => 'Tăng huyết áp',
        ]);

        $this->actingAs($patient, 'sanctum')
            ->getJson('/api/profile')
            ->assertOk()
            ->assertJsonPath('data.patient_profile.phone', '0901234567')
            ->assertJsonPath('data.patient_profile.medical_history', 'Tăng huyết áp');
    }

    public function test_patient_can_update_profile(): void
    {
        $patient = User::factory()->create(['role' => User::ROLE_PATIENT]);
        $patient->patientProfile()->create(['phone' => '0901234567']);

        $this->actingAs($patient, 'sanctum')
            ->putJson('/api/profile', [
                'phone' => '0912345678',
                'allergies' => 'Penicillin',
            ])
            ->assertOk()
            ->assertJsonPath('data.patient_profile.phone', '0912345678')
            ->assertJsonPath('data.patient_profile.allergies', 'Penicillin');
    }

    private function registerPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Nguyễn Văn An',
            'email' => 'an@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0901234567',
            'dob' => '2003-01-18',
            'gender' => 'male',
            'insurance_number' => 'HS123456789',
            'citizen_id' => '001203000000',
            'address' => 'Hà Nội',
            'emergency_contact_name' => 'Nguyễn Văn B',
            'emergency_contact_phone' => '0912345678',
            'medical_history' => 'Tăng huyết áp',
            'allergies' => 'Penicillin',
        ], $overrides);
    }

    private function ticketData(array $overrides = []): array
    {
        return array_merge([
            'patient_name' => 'Bệnh nhân API',
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
