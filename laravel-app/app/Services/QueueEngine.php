<?php

namespace App\Services;

use App\Models\QueueTicket;
use App\Models\QueueTicketEvent;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QueueEngine
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {
    }

    public function createTicket(array $data, ?int $userId = null): QueueTicket
    {
        return DB::transaction(function () use ($data, $userId) {
            $ticketNumber = QueueTicket::nextTicketNumber();
            $department = $data['department'] ?? $this->departmentForService($data['service_type']);
            $serviceType = $data['service_type'];
            $paymentStatus = $data['payment_status'] ?? $this->defaultPaymentStatus($serviceType);

            $ticket = QueueTicket::create([
                'user_id' => $userId,
                'patient_name' => $data['patient_name'],
                'patient_phone' => $data['patient_phone'] ?? null,
                'channel' => $data['channel'],
                'service_type' => $serviceType,
                'department' => $department,
                'priority_level' => $this->normalizePriority((int) ($data['priority_level'] ?? 0), $serviceType, $department),
                'payment_status' => $paymentStatus,
                'ticket_number' => $ticketNumber,
                'queue_number' => QueueTicket::makeQueueNumber($ticketNumber, $department),
                'status' => $this->initialStatus($paymentStatus),
                'estimated_wait' => QueueTicket::calculateEstimatedWait($department),
                'activated_at' => $paymentStatus === QueueTicket::PAYMENT_PENDING ? null : now(),
                'notes' => $data['notes'] ?? null,
            ]);

            $this->log($ticket, 'created', null, $ticket->status);

            return $ticket;
        });
    }

    public function callNext(string $departmentId): ?QueueTicket
    {
        return DB::transaction(function () use ($departmentId) {
            if ($this->departmentHasActiveTicket($departmentId)) {
                throw new DomainException('Khoa này đang có bệnh nhân được gọi hoặc đang khám.');
            }

            $ticket = QueueTicket::query()
                ->where('department', $departmentId)
                ->where('status', QueueTicket::STATUS_READY)
                ->orderByDesc('priority_level')
                ->orderBy('created_at')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (! $ticket) {
                return null;
            }

            $oldStatus = $ticket->status;
            $ticket->update([
                'status' => QueueTicket::STATUS_CALLING,
                'called_at' => now(),
                'estimated_wait' => 0,
            ]);
            $this->log($ticket, 'called', $oldStatus, QueueTicket::STATUS_CALLING);
            $this->notificationService->notifyCalling($ticket->fresh());

            return $ticket->fresh();
        });
    }

    public function markServing(int $ticketId): QueueTicket
    {
        $ticket = $this->ticket($ticketId);
        $this->assertStatus($ticket, [QueueTicket::STATUS_CALLING], 'Chỉ số đang gọi mới có thể chuyển sang đang khám.');

        $this->ensureNoOtherServingTicket($ticket);

        $oldStatus = $ticket->status;
        $ticket->update(['status' => QueueTicket::STATUS_SERVING]);
        $this->log($ticket, 'serving', $oldStatus, QueueTicket::STATUS_SERVING);

        return $ticket->fresh();
    }

    public function markMissed(int $ticketId): QueueTicket
    {
        $ticket = $this->ticket($ticketId);
        $this->assertStatus($ticket, [QueueTicket::STATUS_CALLING], 'Chỉ số đang gọi mới có thể đánh dấu vắng mặt.');

        $oldStatus = $ticket->status;
        $ticket->update([
            'status' => QueueTicket::STATUS_MISSED,
            'missed_at' => now(),
            'no_show_at' => now(),
        ]);
        $this->log($ticket, 'missed', $oldStatus, QueueTicket::STATUS_MISSED);

        return $ticket->fresh();
    }

    public function recall(int $ticketId): QueueTicket
    {
        $ticket = $this->ticket($ticketId);
        $this->assertStatus($ticket, [QueueTicket::STATUS_MISSED], 'Chỉ số vắng mặt mới có thể gọi lại.');

        if ($this->departmentHasActiveTicket($ticket->department)) {
            throw new DomainException('Khoa này đang có bệnh nhân được gọi hoặc đang khám.');
        }

        $oldStatus = $ticket->status;
        $ticket->update([
            'status' => QueueTicket::STATUS_CALLING,
            'called_at' => now(),
            'notes' => trim(($ticket->notes ?? '') . ' | Đã gọi lại'),
        ]);
        $this->log($ticket, 'recalled', $oldStatus, QueueTicket::STATUS_CALLING);

        return $ticket->fresh();
    }

    public function complete(int $ticketId): QueueTicket
    {
        $ticket = $this->ticket($ticketId);
        $this->assertStatus($ticket, [QueueTicket::STATUS_SERVING], 'Chỉ số đang khám mới có thể hoàn thành.');

        $oldStatus = $ticket->status;
        $ticket->update([
            'status' => QueueTicket::STATUS_COMPLETED,
            'completed_at' => now(),
            'estimated_wait' => 0,
        ]);
        $this->log($ticket, 'completed', $oldStatus, QueueTicket::STATUS_COMPLETED);

        return $ticket->fresh();
    }

    public function cancel(int $ticketId): QueueTicket
    {
        $ticket = $this->ticket($ticketId);
        $this->assertMutable($ticket);

        $oldStatus = $ticket->status;
        $ticket->update([
            'status' => QueueTicket::STATUS_CANCELLED,
            'estimated_wait' => 0,
        ]);
        $this->log($ticket, 'cancelled', $oldStatus, QueueTicket::STATUS_CANCELLED);

        return $ticket->fresh();
    }

    public function activatePayment(QueueTicket $ticket): QueueTicket
    {
        $this->assertStatus($ticket, [QueueTicket::STATUS_DRAFT, QueueTicket::STATUS_WAITING_PAYMENT], 'Chỉ phiếu chờ thanh toán mới có thể xác nhận HIS.');

        $oldStatus = $ticket->status;
        $ticket->update([
            'payment_status' => QueueTicket::PAYMENT_PAID,
            'status' => QueueTicket::STATUS_READY,
            'activated_at' => now(),
            'estimated_wait' => QueueTicket::calculateEstimatedWait($ticket->department),
        ]);
        $this->log($ticket, 'payment_activated', $oldStatus, QueueTicket::STATUS_READY);

        return $ticket->fresh();
    }

    public function insertEmergency(array $data, ?int $userId = null): QueueTicket
    {
        $data['service_type'] = QueueTicket::SERVICE_CAP_CUU;
        $data['department'] = QueueTicket::DEPARTMENT_EMERGENCY;
        $data['priority_level'] = 5;
        $data['payment_status'] = QueueTicket::PAYMENT_EXEMPTED;

        return $this->createTicket($data, $userId);
    }

    private function ticket(int $ticketId): QueueTicket
    {
        return QueueTicket::query()->findOrFail($ticketId);
    }

    private function assertMutable(QueueTicket $ticket): void
    {
        if ($ticket->isTerminal()) {
            throw new DomainException('Phiếu đã hoàn thành hoặc đã hủy không thể chỉnh sửa.');
        }
    }

    private function assertStatus(QueueTicket $ticket, array $allowedStatuses, string $message): void
    {
        $this->assertMutable($ticket);

        if (! in_array($ticket->status, $allowedStatuses, true)) {
            throw new DomainException($message);
        }
    }

    private function departmentForService(string $serviceType): string
    {
        return match ($serviceType) {
            QueueTicket::SERVICE_CAP_CUU => QueueTicket::DEPARTMENT_EMERGENCY,
            QueueTicket::SERVICE_BHYT => QueueTicket::DEPARTMENT_INSURANCE,
            default => QueueTicket::DEPARTMENT_INTERNAL,
        };
    }

    private function defaultPaymentStatus(string $serviceType): string
    {
        return $serviceType === QueueTicket::SERVICE_CAP_CUU
            ? QueueTicket::PAYMENT_EXEMPTED
            : QueueTicket::PAYMENT_PENDING;
    }

    private function initialStatus(string $paymentStatus): string
    {
        return $paymentStatus === QueueTicket::PAYMENT_PENDING
            ? QueueTicket::STATUS_WAITING_PAYMENT
            : QueueTicket::STATUS_READY;
    }

    private function normalizePriority(int $priorityLevel, string $serviceType, string $department): int
    {
        if ($serviceType === QueueTicket::SERVICE_CAP_CUU || $department === QueueTicket::DEPARTMENT_EMERGENCY) {
            return 5;
        }

        return max(0, min($priorityLevel, 4));
    }

    private function departmentHasActiveTicket(string $department): bool
    {
        return QueueTicket::query()
            ->where('department', $department)
            ->whereIn('status', QueueTicket::inProgressStatuses())
            ->exists();
    }

    private function ensureNoOtherServingTicket(QueueTicket $ticket): void
    {
        $hasOtherServingTicket = QueueTicket::query()
            ->where('department', $ticket->department)
            ->where('id', '!=', $ticket->id)
            ->where('status', QueueTicket::STATUS_SERVING)
            ->exists();

        if ($hasOtherServingTicket) {
            throw new DomainException('Khoa này đã có bệnh nhân đang khám.');
        }
    }

    private function log(QueueTicket $ticket, string $action, ?string $oldStatus, string $newStatus): void
    {
        QueueTicketEvent::create([
            'queue_ticket_id' => $ticket->id,
            'action' => $action,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'performed_by' => Auth::id(),
        ]);
    }
}
