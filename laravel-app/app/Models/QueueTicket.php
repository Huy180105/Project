<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QueueTicket extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_WAITING_PAYMENT = 'waiting_payment';
    public const STATUS_READY = 'ready';
    public const STATUS_CALLING = 'calling';
    public const STATUS_SERVING = 'serving';
    public const STATUS_MISSED = 'missed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_EXEMPTED = 'exempted';

    public const CHANNEL_WEB = 'web';
    public const CHANNEL_KIOSK = 'kiosk';
    public const CHANNEL_COUNTER = 'counter';
    public const CHANNEL_PHONE = 'phone';
    public const CHANNEL_MOBILE = 'mobile';

    public const SERVICE_BHYT = 'BHYT';
    public const SERVICE_DICH_VU = 'Dịch vụ';
    public const SERVICE_CAP_CUU = 'Cấp cứu';
    public const SERVICE_NGUOI_UU_TIEN = 'Người ưu tiên';

    public const DEPARTMENT_INTERNAL = 'Nội tổng quát';
    public const DEPARTMENT_EMERGENCY = 'Cấp cứu';
    public const DEPARTMENT_CARDIOLOGY = 'Tim mạch';
    public const DEPARTMENT_PEDIATRICS = 'Nhi khoa';
    public const DEPARTMENT_ENT = 'Tai mũi họng';
    public const DEPARTMENT_INSURANCE = 'BHYT';

    protected $fillable = [
        'user_id',
        'patient_name',
        'patient_phone',
        'channel',
        'service_type',
        'department',
        'priority_level',
        'payment_status',
        'ticket_number',
        'queue_number',
        'status',
        'estimated_wait',
        'activated_at',
        'called_at',
        'missed_at',
        'completed_at',
        'no_show_at',
        'notes',
        'external_reference',
    ];

    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
            'called_at' => 'datetime',
            'missed_at' => 'datetime',
            'completed_at' => 'datetime',
            'no_show_at' => 'datetime',
        ];
    }

    public static function channels(): array
    {
        return array_keys(self::channelLabels());
    }

    public static function channelLabels(): array
    {
        return [
            self::CHANNEL_WEB => 'Web nội bộ',
            self::CHANNEL_KIOSK => 'Kiosk bệnh viện',
            self::CHANNEL_COUNTER => 'Quầy tiếp nhận',
            self::CHANNEL_PHONE => 'Điện thoại',
            self::CHANNEL_MOBILE => 'Ứng dụng di động',
        ];
    }

    public static function serviceTypes(): array
    {
        return [
            self::SERVICE_BHYT,
            self::SERVICE_DICH_VU,
            self::SERVICE_CAP_CUU,
            self::SERVICE_NGUOI_UU_TIEN,
        ];
    }

    public static function departments(): array
    {
        return [
            self::DEPARTMENT_INTERNAL,
            self::DEPARTMENT_EMERGENCY,
            self::DEPARTMENT_CARDIOLOGY,
            self::DEPARTMENT_PEDIATRICS,
            self::DEPARTMENT_ENT,
            self::DEPARTMENT_INSURANCE,
        ];
    }

    public static function paymentStatuses(): array
    {
        return array_keys(self::paymentStatusLabels());
    }

    public static function paymentStatusLabels(): array
    {
        return [
            self::PAYMENT_PENDING => 'Chờ xác nhận HIS/thanh toán',
            self::PAYMENT_PAID => 'Đã xác nhận HIS/thanh toán',
            self::PAYMENT_EXEMPTED => 'Miễn xác nhận trước',
        ];
    }

    public static function priorityLevels(): array
    {
        return [
            0 => '0 - Bình thường',
            2 => '2 - Ưu tiên theo đối tượng',
            3 => '3 - Ưu tiên cao',
            4 => '4 - Triệu chứng nặng cần điều dưỡng đánh giá',
            5 => '5 - Cấp cứu',
        ];
    }

    public static function priorityReasons(): array
    {
        return [
            'normal' => 'Không thuộc diện ưu tiên',
            'emergency' => 'Người bệnh trong tình trạng cấp cứu',
            'child_under_6' => 'Trẻ em dưới 6 tuổi',
            'pregnant' => 'Phụ nữ có thai',
            'disabled_severe' => 'Người khuyết tật nặng hoặc đặc biệt nặng',
            'elderly_75' => 'Người từ đủ 75 tuổi trở lên',
            'meritorious' => 'Người có công với cách mạng',
            'severe_symptoms' => 'Triệu chứng nặng cần điều dưỡng đánh giá',
        ];
    }

    public static function mobilePriorityReasonAliases(): array
    {
        return [
            'none' => 'normal',
            'severe_disability' => 'disabled_severe',
            'revolutionary_contributor' => 'meritorious',
        ];
    }

    public static function departmentNameForId(int $departmentId): ?string
    {
        return self::departments()[$departmentId - 1] ?? null;
    }

    public static function departmentIdForName(string $department): ?int
    {
        $index = array_search($department, self::departments(), true);

        return $index === false ? null : $index + 1;
    }

    public static function priorityLevelForReason(string $reason): int
    {
        return match ($reason) {
            'emergency' => 5,
            'severe_symptoms' => 4,
            'child_under_6', 'disabled_severe', 'elderly_75' => 3,
            'pregnant', 'meritorious' => 2,
            default => 0,
        };
    }

    public static function statuses(): array
    {
        return array_keys(self::statusLabels());
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'Nháp',
            self::STATUS_WAITING_PAYMENT => 'Chờ thanh toán',
            self::STATUS_READY => 'Sẵn sàng gọi',
            self::STATUS_CALLING => 'Đang gọi',
            self::STATUS_SERVING => 'Đang khám',
            self::STATUS_MISSED => 'Vắng mặt',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    public static function terminalStatuses(): array
    {
        return [self::STATUS_COMPLETED, self::STATUS_CANCELLED];
    }

    public static function inProgressStatuses(): array
    {
        return [self::STATUS_CALLING, self::STATUS_SERVING];
    }

    public static function nextTicketNumber(): int
    {
        return (int) self::max('ticket_number') + 1;
    }

    public static function makeQueueNumber(int $ticketNumber, string $department): string
    {
        $prefix = match ($department) {
            self::DEPARTMENT_EMERGENCY => 'E',
            self::DEPARTMENT_CARDIOLOGY => 'C',
            self::DEPARTMENT_PEDIATRICS => 'P',
            self::DEPARTMENT_ENT => 'T',
            self::DEPARTMENT_INSURANCE => 'I',
            default => 'A',
        };

        return $prefix . str_pad((string) $ticketNumber, 3, '0', STR_PAD_LEFT);
    }

    public static function calculateEstimatedWait(?string $department = null): int
    {
        return (int) self::query()
            ->whereIn('status', [self::STATUS_WAITING_PAYMENT, self::STATUS_READY])
            ->when($department, fn ($query) => $query->where('department', $department))
            ->count() * 4;
    }

    public static function positionInQueue(self $ticket): int
    {
        if ($ticket->status !== self::STATUS_READY) {
            return 0;
        }

        return self::query()
            ->where('status', self::STATUS_READY)
            ->where('department', $ticket->department)
            ->where(function ($query) use ($ticket) {
                $query->where('priority_level', '>', $ticket->priority_level)
                    ->orWhere(function ($sub) use ($ticket) {
                        $sub->where('priority_level', $ticket->priority_level)
                            ->where('created_at', '<', $ticket->created_at);
                    });
            })
            ->count() + 1;
    }

    public static function currentCallingNumber(string $department): ?string
    {
        return self::query()
            ->where('department', $department)
            ->where('status', self::STATUS_CALLING)
            ->latest('called_at')
            ->first()
            ?->displayNumber();
    }

    public static function roomForService(string $serviceType, ?string $department = null): string
    {
        return match ($department) {
            self::DEPARTMENT_EMERGENCY => 'Phòng Cấp cứu 101',
            self::DEPARTMENT_CARDIOLOGY => 'Phòng Tim mạch 205',
            self::DEPARTMENT_PEDIATRICS => 'Phòng Nhi 301',
            self::DEPARTMENT_ENT => 'Phòng Tai mũi họng 208',
            self::DEPARTMENT_INSURANCE => 'Quầy BHYT 02',
            default => match ($serviceType) {
                self::SERVICE_CAP_CUU => 'Phòng Cấp cứu 101',
                self::SERVICE_NGUOI_UU_TIEN => 'Phòng Ưu tiên 207',
                self::SERVICE_DICH_VU => 'Phòng Dịch vụ 210',
                default => 'Phòng Nội tổng quát 203',
            },
        };
    }

    public function isPriority(): bool
    {
        return $this->priority_level > 0;
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, self::terminalStatuses(), true);
    }

    public function events(): HasMany
    {
        return $this->hasMany(QueueTicketEvent::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function displayNumber(): string
    {
        return $this->queue_number ?: self::makeQueueNumber((int) $this->ticket_number, $this->department);
    }

    public function priorityReasonKey(): string
    {
        return match ($this->priority_level) {
            5 => 'emergency',
            4 => 'severe_symptoms',
            3 => str_contains((string) $this->notes, self::priorityReasons()['child_under_6']) ? 'child_under_6'
                : (str_contains((string) $this->notes, self::priorityReasons()['disabled_severe']) ? 'disabled_severe' : 'elderly_75'),
            2 => str_contains((string) $this->notes, self::priorityReasons()['pregnant']) ? 'pregnant' : 'meritorious',
            default => 'normal',
        };
    }
}
