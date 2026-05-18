<?php

namespace App\Http\Requests;

use App\Models\QueueTicket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreQueueTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_name' => 'required|string|max:255',
            'channel' => 'required|string|in:' . implode(',', QueueTicket::channels()),
            'service_type' => 'required|string|in:' . implode(',', QueueTicket::serviceTypes()),
            'department' => 'required|string|in:' . implode(',', QueueTicket::departments()),
            'payment_status' => 'required|string|in:' . implode(',', QueueTicket::paymentStatuses()),
            'priority_level' => 'required|integer|min:0|max:5',
            'priority_reason' => 'required|string|in:' . implode(',', array_keys(QueueTicket::priorityReasons())),
            'notes' => 'nullable|string|max:1000',
            'emergency' => 'nullable|boolean',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $data = $this->validated();
                $priorityLevel = (int) $data['priority_level'];
                $priorityReason = $data['priority_reason'];
                $expectedLevel = QueueTicket::priorityLevelForReason($priorityReason);
                $isEmergency = $this->boolean('emergency')
                    || $data['service_type'] === QueueTicket::SERVICE_CAP_CUU
                    || $data['department'] === QueueTicket::DEPARTMENT_EMERGENCY
                    || $priorityReason === 'emergency';

                if ($priorityReason === 'normal' && $priorityLevel !== 0) {
                    $validator->errors()->add('priority_reason', 'Nếu không thuộc diện ưu tiên thì mức ưu tiên phải là 0.');
                }
                if ($priorityReason !== 'normal' && $priorityLevel === 0) {
                    $validator->errors()->add('priority_level', 'Đã chọn lý do ưu tiên thì phải chọn mức ưu tiên phù hợp.');
                }
                if ($isEmergency && $priorityLevel !== 5) {
                    $validator->errors()->add('priority_level', 'Ca cấp cứu phải dùng mức ưu tiên 5.');
                }
                if (! $isEmergency && $priorityLevel === 5) {
                    $validator->errors()->add('priority_level', 'Mức 5 chỉ dùng cho người bệnh cấp cứu.');
                }
                if ($priorityLevel !== $expectedLevel) {
                    $validator->errors()->add('priority_level', 'Mức ưu tiên chưa khớp với lý do ưu tiên.');
                }
                if ($priorityLevel > 0 && blank($data['notes'] ?? null)) {
                    $validator->errors()->add('notes', 'Trường hợp ưu tiên cần ghi chú căn cứ.');
                }
                if ($isEmergency && $data['payment_status'] === QueueTicket::PAYMENT_PENDING) {
                    $validator->errors()->add('payment_status', 'Ca cấp cứu không được chờ xác nhận thanh toán trước khi gọi khám.');
                }
            },
        ];
    }

    public function payload(): array
    {
        $data = $this->validated();
        $notes = trim((string) ($data['notes'] ?? ''));
        $reason = QueueTicket::priorityReasons()[$data['priority_reason']] ?? null;

        if ($reason && $data['priority_reason'] !== 'normal') {
            $notes = trim($notes . ' | Lý do ưu tiên: ' . $reason);
        }

        $data['notes'] = $notes ?: null;

        return $data;
    }
}
