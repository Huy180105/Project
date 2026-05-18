<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\QueueTicket;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    public function notifyNearTurn(QueueTicket $ticket): ?NotificationLog
    {
        if ($this->shouldNotDuplicate($ticket, NotificationLog::TYPE_NEAR_TURN)) {
            return null;
        }

        $remaining = max(0, QueueTicket::positionInQueue($ticket) - 1);

        return $this->createAndDispatch(
            ticket: $ticket,
            type: NotificationLog::TYPE_NEAR_TURN,
            title: 'Sắp đến lượt khám',
            message: "Phiếu {$ticket->displayNumber()} còn {$remaining} lượt nữa. Vui lòng di chuyển gần phòng khám.",
            payload: [
                'queue_number' => $ticket->displayNumber(),
                'remaining_before_me' => $remaining,
                'department' => $ticket->department,
            ],
        );
    }

    public function notifyCalling(QueueTicket $ticket): ?NotificationLog
    {
        if ($this->shouldNotDuplicate($ticket, NotificationLog::TYPE_CALLING)) {
            return null;
        }

        return $this->createAndDispatch(
            ticket: $ticket,
            type: NotificationLog::TYPE_CALLING,
            title: 'Đến lượt khám',
            message: "Phiếu {$ticket->displayNumber()} đang được gọi. Vui lòng vào phòng khám.",
            payload: [
                'queue_number' => $ticket->displayNumber(),
                'department' => $ticket->department,
            ],
        );
    }

    public function shouldNotDuplicate(QueueTicket $ticket, string $type): bool
    {
        return $ticket->notificationLogs()->where('type', $type)->exists();
    }

    private function createAndDispatch(
        QueueTicket $ticket,
        string $type,
        string $title,
        string $message,
        array $payload,
    ): NotificationLog {
        $log = $ticket->notificationLogs()->create([
            'patient_name' => $ticket->patient_name,
            'patient_phone' => $ticket->patient_phone,
            'type' => $type,
            'channel' => NotificationLog::CHANNEL_N8N_WEBHOOK,
            'title' => $title,
            'message' => $message,
            'status' => NotificationLog::STATUS_PENDING,
            'payload' => $payload,
        ]);

        $webhookUrl = config('services.n8n.notification_webhook_url');

        if (! $webhookUrl) {
            return $log;
        }

        try {
            $response = Http::post($webhookUrl, [
                'notification_id' => $log->id,
                'ticket_id' => $ticket->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'patient_name' => $ticket->patient_name,
                'patient_phone' => $ticket->patient_phone,
                'payload' => $payload,
            ]);

            $log->update([
                'status' => $response->successful()
                    ? NotificationLog::STATUS_SENT
                    : NotificationLog::STATUS_FAILED,
                'sent_at' => $response->successful() ? now() : null,
            ]);
        } catch (\Throwable) {
            $log->update([
                'status' => NotificationLog::STATUS_FAILED,
            ]);
        }

        return $log->fresh();
    }
}
