<?php

namespace App\Http\Resources;

use App\Models\QueueTicket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QueueTicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $queuePosition = QueueTicket::positionInQueue($this->resource);

        return [
            'id' => $this->id,
            'queue_number' => $this->displayNumber(),
            'patient_name' => $this->patient_name,
            'patient_phone' => $this->patient_phone,
            'department' => [
                'id' => QueueTicket::departmentIdForName($this->department),
                'name' => $this->department,
                'room_number' => QueueTicket::roomForService($this->service_type, $this->department),
            ],
            'status' => $this->status,
            'status_label' => QueueTicket::statusLabels()[$this->status],
            'channel' => $this->channel,
            'payment_status' => $this->payment_status,
            'payment_status_label' => QueueTicket::paymentStatusLabels()[$this->payment_status],
            'priority_level' => $this->priority_level,
            'priority_reason' => $this->priorityReasonKey(),
            'priority_reason_label' => QueueTicket::priorityReasons()[$this->priorityReasonKey()],
            'estimated_wait_time' => $this->estimated_wait,
            'queue_position' => $queuePosition,
            'position_in_queue' => $queuePosition,
            'remaining_before_me' => $queuePosition > 0 ? $queuePosition - 1 : 0,
            'current_calling_number' => QueueTicket::currentCallingNumber($this->department),
            'department_room' => QueueTicket::roomForService($this->service_type, $this->department),
            'called_at' => $this->called_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
