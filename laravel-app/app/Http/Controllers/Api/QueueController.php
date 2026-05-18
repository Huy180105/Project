<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\RespondsWithApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientTicketRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\QueueTicketResource;
use App\Models\QueueTicket;
use App\Services\NotificationService;
use App\Services\QueueEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    use RespondsWithApi;

    public function departments(): JsonResponse
    {
        $departments = collect(QueueTicket::departments())
            ->values()
            ->map(function (string $department, int $index) {
                $activeTicket = QueueTicket::query()
                    ->where('department', $department)
                    ->whereIn('status', QueueTicket::inProgressStatuses())
                    ->latest('called_at')
                    ->first();

                return [
                    'id' => $index + 1,
                    'name' => $department,
                    'room_number' => QueueTicket::roomForService(QueueTicket::SERVICE_DICH_VU, $department),
                    'current_number' => $activeTicket?->displayNumber(),
                    'average_time_per_patient' => 4,
                ];
            });

        return $this->success(DepartmentResource::collection($departments), 'Tải danh sách khoa thành công.');
    }

    public function store(StorePatientTicketRequest $request, QueueEngine $queueEngine): JsonResponse
    {
        $ticket = $queueEngine->createTicket($request->payload($request->user()), $request->user()->id);

        return $this->success(new QueueTicketResource($ticket), 'Tạo phiếu khám thành công.', 201);
    }

    public function myTicket(Request $request): JsonResponse
    {
        $ticket = $request->user()
            ->queueTickets()
            ->whereNotIn('status', QueueTicket::terminalStatuses())
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        return $this->success(
            $ticket ? new QueueTicketResource($ticket) : null,
            $ticket ? 'Tải phiếu hiện tại thành công.' : 'Bạn chưa có phiếu đang hoạt động.',
        );
    }

    public function status(
        Request $request,
        QueueTicket $ticket,
        NotificationService $notificationService,
    ): JsonResponse {
        $this->authorizeTicket($request, $ticket);

        $position = QueueTicket::positionInQueue($ticket);

        if (
            in_array($ticket->status, [QueueTicket::STATUS_READY, QueueTicket::STATUS_CALLING], true) &&
            $position > 0 &&
            $position - 1 <= 5
        ) {
            $notificationService->notifyNearTurn($ticket);
        }

        return $this->success(new QueueTicketResource($ticket), 'Tải trạng thái phiếu thành công.');
    }

    public function display(string $department): JsonResponse
    {
        abort_unless(in_array($department, QueueTicket::departments(), true), 404);

        $calling = QueueTicket::where('department', $department)
            ->where('status', QueueTicket::STATUS_CALLING)
            ->latest('called_at')
            ->first();
        $serving = QueueTicket::where('department', $department)
            ->where('status', QueueTicket::STATUS_SERVING)
            ->latest('called_at')
            ->first();
        $next = QueueTicket::where('department', $department)
            ->where('status', QueueTicket::STATUS_READY)
            ->orderByDesc('priority_level')
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        return $this->success([
            'department' => $department,
            'calling' => $calling ? new QueueTicketResource($calling) : null,
            'serving' => $serving ? new QueueTicketResource($serving) : null,
            'next' => QueueTicketResource::collection($next),
        ], 'Tải màn hình hàng đợi thành công.');
    }

    public function qr(Request $request, QueueTicket $ticket): JsonResponse
    {
        $this->authorizeTicket($request, $ticket);

        $payload = [
            'ticket_id' => $ticket->id,
            'queue_number' => $ticket->displayNumber(),
            'department_id' => QueueTicket::departmentIdForName($ticket->department),
            'patient_name' => $ticket->patient_name,
            'issued_at' => $ticket->created_at?->toISOString(),
        ];

        return $this->success([
            ...$payload,
            'qr_payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ], 'Tạo dữ liệu QR thành công.');
    }

    private function authorizeTicket(Request $request, QueueTicket $ticket): void
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);
    }
}
