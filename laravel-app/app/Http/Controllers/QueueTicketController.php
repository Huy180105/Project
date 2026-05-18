<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQueueTicketRequest;
use App\Models\QueueTicket;
use App\Models\User;
use App\Services\QueueEngine;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QueueTicketController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'department' => 'nullable|string|in:' . implode(',', QueueTicket::departments()),
            'priority_level' => 'nullable|integer|in:' . implode(',', array_keys(QueueTicket::priorityLevels())),
            'payment_status' => 'nullable|string|in:' . implode(',', QueueTicket::paymentStatuses()),
        ]);

        $tickets = QueueTicket::query()
            ->when($filters['department'] ?? null, fn ($query, $department) => $query->where('department', $department))
            ->when(array_key_exists('priority_level', $filters), fn ($query) => $query->where('priority_level', $filters['priority_level']))
            ->when($filters['payment_status'] ?? null, fn ($query, $paymentStatus) => $query->where('payment_status', $paymentStatus))
            ->orderByRaw("CASE status WHEN 'calling' THEN 0 WHEN 'serving' THEN 1 WHEN 'ready' THEN 2 WHEN 'waiting_payment' THEN 3 WHEN 'missed' THEN 4 WHEN 'completed' THEN 5 WHEN 'cancelled' THEN 6 ELSE 7 END")
            ->orderByDesc('priority_level')
            ->orderBy('created_at')
            ->limit(100)
            ->get();

        return view('queue-tickets.index', [
            'tickets' => $tickets,
            'filters' => $filters,
            'waiting' => QueueTicket::where('status', QueueTicket::STATUS_WAITING_PAYMENT)->count(),
            'ready' => QueueTicket::where('status', QueueTicket::STATUS_READY)->count(),
            'calling' => QueueTicket::where('status', QueueTicket::STATUS_CALLING)->count(),
            'missed' => QueueTicket::where('status', QueueTicket::STATUS_MISSED)->count(),
            'completed' => QueueTicket::where('status', QueueTicket::STATUS_COMPLETED)->whereDate('completed_at', now())->count(),
            'departments' => QueueTicket::departments(),
            'priorityLevels' => QueueTicket::priorityLevels(),
            'paymentStatusLabels' => QueueTicket::paymentStatusLabels(),
        ]);
    }

    public function create(): View
    {
        return view('queue-tickets.create', $this->formOptions());
    }

    public function status(): JsonResponse
    {
        $callingTicket = QueueTicket::where('status', QueueTicket::STATUS_CALLING)->latest('called_at')->first();
        $nextTicket = QueueTicket::where('status', QueueTicket::STATUS_READY)->orderByDesc('priority_level')->orderBy('created_at')->first();

        return response()->json([
            'waiting' => QueueTicket::where('status', QueueTicket::STATUS_WAITING_PAYMENT)->count(),
            'ready' => QueueTicket::where('status', QueueTicket::STATUS_READY)->count(),
            'missed' => QueueTicket::where('status', QueueTicket::STATUS_MISSED)->count(),
            'completed_today' => QueueTicket::where('status', QueueTicket::STATUS_COMPLETED)->whereDate('completed_at', now())->count(),
            'average_estimated_wait' => round((float) QueueTicket::whereIn('status', [QueueTicket::STATUS_WAITING_PAYMENT, QueueTicket::STATUS_READY])->avg('estimated_wait'), 1),
            'calling_ticket' => $callingTicket ? [
                'queue_number' => $callingTicket->displayNumber(),
                'patient_name' => $callingTicket->patient_name,
                'room' => QueueTicket::roomForService($callingTicket->service_type, $callingTicket->department),
            ] : null,
            'next_ticket' => $nextTicket ? [
                'queue_number' => $nextTicket->displayNumber(),
                'patient_name' => $nextTicket->patient_name,
                'room' => QueueTicket::roomForService($nextTicket->service_type, $nextTicket->department),
            ] : null,
        ]);
    }

    public function store(StoreQueueTicketRequest $request, QueueEngine $queueEngine): RedirectResponse
    {
        abort_unless(Auth::user()?->hasRole([User::ROLE_RECEPTIONIST, User::ROLE_NURSE, User::ROLE_ADMIN]), 403);

        $data = $request->payload();
        $ticket = $request->boolean('emergency')
            ? $queueEngine->insertEmergency($data, Auth::id())
            : $queueEngine->createTicket($data, Auth::id());

        return redirect()->route('queue-tickets.index')->with('success', "Đã tạo số {$ticket->displayNumber()} cho {$ticket->patient_name}.");
    }

    public function callNext(string $department, QueueEngine $queueEngine): RedirectResponse
    {
        $this->authorizeStaffAction();
        abort_unless(in_array($department, QueueTicket::departments(), true), 404);

        try {
            $ticket = $queueEngine->callNext($department);
        } catch (DomainException $exception) {
            return back()->withErrors(['queue' => $exception->getMessage()]);
        }

        return $ticket
            ? back()->with('success', "Đang gọi số {$ticket->displayNumber()}.")
            : back()->with('success', 'Khoa này chưa có số sẵn sàng để gọi.');
    }

    public function serving(QueueTicket $ticket, QueueEngine $queueEngine): RedirectResponse { return $this->transition(fn () => $queueEngine->markServing($ticket->id)); }
    public function missed(QueueTicket $ticket, QueueEngine $queueEngine): RedirectResponse { return $this->transition(fn () => $queueEngine->markMissed($ticket->id)); }
    public function recall(QueueTicket $ticket, QueueEngine $queueEngine): RedirectResponse { return $this->transition(fn () => $queueEngine->recall($ticket->id)); }
    public function complete(QueueTicket $ticket, QueueEngine $queueEngine): RedirectResponse { return $this->transition(fn () => $queueEngine->complete($ticket->id)); }
    public function cancel(QueueTicket $ticket, QueueEngine $queueEngine): RedirectResponse { return $this->transition(fn () => $queueEngine->cancel($ticket->id)); }
    public function activatePayment(QueueTicket $ticket, QueueEngine $queueEngine): RedirectResponse { return $this->transition(fn () => $queueEngine->activatePayment($ticket)); }

    private function transition(callable $callback): RedirectResponse
    {
        $this->authorizeStaffAction();

        try {
            $callback();
        } catch (DomainException $exception) {
            return back()->withErrors(['queue' => $exception->getMessage()]);
        }

        return back()->with('success', 'Cập nhật hàng đợi đã được thực hiện.');
    }

    private function authorizeStaffAction(): void
    {
        abort_unless(Auth::user()?->hasRole([User::ROLE_RECEPTIONIST, User::ROLE_NURSE, User::ROLE_DOCTOR, User::ROLE_ADMIN]), 403);
    }

    private function formOptions(): array
    {
        return [
            'channels' => QueueTicket::channels(),
            'services' => QueueTicket::serviceTypes(),
            'departments' => QueueTicket::departments(),
            'paymentStatuses' => QueueTicket::paymentStatuses(),
            'channelLabels' => QueueTicket::channelLabels(),
            'paymentStatusLabels' => QueueTicket::paymentStatusLabels(),
            'priorityLevels' => QueueTicket::priorityLevels(),
            'priorityReasons' => QueueTicket::priorityReasons(),
        ];
    }
}
