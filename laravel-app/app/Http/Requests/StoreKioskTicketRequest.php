<?php

namespace App\Http\Requests;

use App\Models\QueueTicket;
use Illuminate\Foundation\Http\FormRequest;

class StoreKioskTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'nullable|string|max:30',
            'department' => 'required|string|in:' . implode(',', QueueTicket::departments()),
            'priority_reason' => 'required|string|in:' . implode(',', array_keys(QueueTicket::priorityReasons())),
        ];
    }

    public function payload(): array
    {
        $data = $this->validated();
        $reason = $data['priority_reason'];
        $isEmergency = $reason === 'emergency' || $data['department'] === QueueTicket::DEPARTMENT_EMERGENCY;
        $priorityLevel = QueueTicket::priorityLevelForReason($reason);

        return [
            'patient_name' => $data['patient_name'],
            'patient_phone' => $data['patient_phone'] ?? null,
            'channel' => QueueTicket::CHANNEL_KIOSK,
            'service_type' => $isEmergency ? QueueTicket::SERVICE_CAP_CUU : QueueTicket::SERVICE_DICH_VU,
            'department' => $isEmergency ? QueueTicket::DEPARTMENT_EMERGENCY : $data['department'],
            'priority_level' => $isEmergency ? 5 : $priorityLevel,
            'payment_status' => $isEmergency ? QueueTicket::PAYMENT_EXEMPTED : QueueTicket::PAYMENT_PENDING,
            'notes' => $reason === 'normal'
                ? null
                : 'Lý do ưu tiên do người bệnh khai tại kiosk: ' . QueueTicket::priorityReasons()[$reason],
        ];
    }
}
