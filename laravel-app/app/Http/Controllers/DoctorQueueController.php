<?php

namespace App\Http\Controllers;

use App\Models\QueueTicket;
use App\Models\User;
use App\Services\QueueEngine;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorQueueController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeDoctorPanel();

        $department = $request->validate([
            'department' => 'nullable|string|in:' . implode(',', QueueTicket::departments()),
        ])['department'] ?? QueueTicket::DEPARTMENT_INTERNAL;

        return view('doctor.queue', [
            'department' => $department,
            'departments' => QueueTicket::departments(),
            'callingTicket' => QueueTicket::where('department', $department)->where('status', QueueTicket::STATUS_CALLING)->latest('called_at')->first(),
            'servingTicket' => QueueTicket::where('department', $department)->where('status', QueueTicket::STATUS_SERVING)->latest('called_at')->first(),
            'readyTickets' => QueueTicket::where('department', $department)->where('status', QueueTicket::STATUS_READY)->orderByDesc('priority_level')->orderBy('created_at')->get(),
            'missedTickets' => QueueTicket::where('department', $department)->where('status', QueueTicket::STATUS_MISSED)->latest('missed_at')->get(),
            'completedTickets' => QueueTicket::where('department', $department)->where('status', QueueTicket::STATUS_COMPLETED)->whereDate('completed_at', now())->latest('completed_at')->get(),
        ]);
    }

    public function callNext(Request $request, QueueEngine $queueEngine): RedirectResponse
    {
        $this->authorizeDoctorPanel();

        $department = $request->validate([
            'department' => 'required|string|in:' . implode(',', QueueTicket::departments()),
        ])['department'];

        try {
            $ticket = $queueEngine->callNext($department);
        } catch (DomainException $exception) {
            return back()->withErrors(['queue' => $exception->getMessage()]);
        }

        if (! $ticket) {
            return back()->with('success', 'Khoa này chưa có bệnh nhân sẵn sàng để gọi.');
        }

        return back()->with('success', "Đang gọi số {$ticket->displayNumber()}.");
    }

    public function markServing(QueueTicket $ticket, QueueEngine $queueEngine): RedirectResponse
    {
        return $this->transition(fn () => $queueEngine->markServing($ticket->id));
    }

    public function markMissed(QueueTicket $ticket, QueueEngine $queueEngine): RedirectResponse
    {
        return $this->transition(fn () => $queueEngine->markMissed($ticket->id));
    }

    public function recall(QueueTicket $ticket, QueueEngine $queueEngine): RedirectResponse
    {
        return $this->transition(fn () => $queueEngine->recall($ticket->id));
    }

    public function complete(QueueTicket $ticket, QueueEngine $queueEngine): RedirectResponse
    {
        return $this->transition(fn () => $queueEngine->complete($ticket->id));
    }

    private function transition(callable $callback): RedirectResponse
    {
        $this->authorizeDoctorPanel();

        try {
            $callback();
        } catch (DomainException $exception) {
            return back()->withErrors(['queue' => $exception->getMessage()]);
        }

        return back()->with('success', 'Đã cập nhật trạng thái hàng đợi.');
    }

    private function authorizeDoctorPanel(): void
    {
        abort_unless(Auth::user()?->hasRole([
            User::ROLE_DOCTOR,
            User::ROLE_NURSE,
            User::ROLE_ADMIN,
        ]), 403);
    }
}
