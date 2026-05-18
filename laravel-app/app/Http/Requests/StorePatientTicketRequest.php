<?php

namespace App\Http\Requests;

use App\Models\QueueTicket;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StorePatientTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPatient() === true;
    }

    public function rules(): array
    {
        return [
            'patient_phone' => 'nullable|string|max:30',
            'department_id' => 'required_without:department|integer|min:1|max:' . count(QueueTicket::departments()),
            'department' => 'required_without:department_id|string|in:' . implode(',', QueueTicket::departments()),
            'priority_reason' => 'required|string|in:' . implode(',', array_merge(
                array_keys(QueueTicket::priorityReasons()),
                array_keys(QueueTicket::mobilePriorityReasonAliases()),
            )),
        ];
    }

    public function payload(User $user): array
    {
        $data = $this->validated();
        $reason = QueueTicket::mobilePriorityReasonAliases()[$data['priority_reason']] ?? $data['priority_reason'];
        $department = isset($data['department_id'])
            ? QueueTicket::departmentNameForId((int) $data['department_id'])
            : $data['department'];
        $isEmergency = $reason === 'emergency' || $department === QueueTicket::DEPARTMENT_EMERGENCY;

        return [
            'patient_name' => $user->name,
            'patient_phone' => ($data['patient_phone'] ?? null) ?: $user->patientProfile?->phone,
            'channel' => QueueTicket::CHANNEL_MOBILE,
            'service_type' => $isEmergency ? QueueTicket::SERVICE_CAP_CUU : QueueTicket::SERVICE_DICH_VU,
            'department' => $isEmergency ? QueueTicket::DEPARTMENT_EMERGENCY : $department,
            'priority_level' => $isEmergency ? 5 : QueueTicket::priorityLevelForReason($reason),
            'payment_status' => $isEmergency ? QueueTicket::PAYMENT_EXEMPTED : QueueTicket::PAYMENT_PENDING,
            'notes' => $reason === 'normal'
                ? null
                : 'Lý do ưu tiên do người bệnh khai trên ứng dụng: ' . QueueTicket::priorityReasons()[$reason],
        ];
    }
}
