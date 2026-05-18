<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQueueTicketRequest;
use App\Models\QueueTicket;
use App\Models\User;
use App\Services\QueueEngine;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReceptionQueueController extends Controller
{
    public function index(): View
    {
        $this->authorizeReception();

        return view('reception.queue', [
            'waitingTickets' => QueueTicket::where('status', QueueTicket::STATUS_WAITING_PAYMENT)->oldest()->get(),
            'readyTickets' => QueueTicket::where('status', QueueTicket::STATUS_READY)->orderByDesc('priority_level')->orderBy('created_at')->get(),
            'cancelledTickets' => QueueTicket::where('status', QueueTicket::STATUS_CANCELLED)->whereDate('updated_at', now())->latest('updated_at')->get(),
            'channelLabels' => QueueTicket::channelLabels(),
            'services' => QueueTicket::serviceTypes(),
            'departments' => QueueTicket::departments(),
            'paymentStatusLabels' => QueueTicket::paymentStatusLabels(),
            'priorityLevels' => QueueTicket::priorityLevels(),
            'priorityReasons' => QueueTicket::priorityReasons(),
        ]);
    }

    public function create(): RedirectResponse
    {
        $this->authorizeReception();

        return redirect()->route('reception.queue.index');
    }

    public function store(StoreQueueTicketRequest $request, QueueEngine $queueEngine): RedirectResponse
    {
        $this->authorizeReception();
        $data = $request->payload();
        $ticket = $request->boolean('emergency') ? $queueEngine->insertEmergency($data, Auth::id()) : $queueEngine->createTicket($data, Auth::id());

        return back()->with('success', "Đã tạo số {$ticket->displayNumber()} cho {$ticket->patient_name}.");
    }

    public function activate(QueueTicket $ticket, QueueEngine $queueEngine): RedirectResponse
    {
        return $this->transition(fn () => $queueEngine->activatePayment($ticket));
    }

    public function cancel(QueueTicket $ticket, QueueEngine $queueEngine): RedirectResponse
    {
        return $this->transition(fn () => $queueEngine->cancel($ticket->id));
    }

    private function transition(callable $callback): RedirectResponse
    {
        $this->authorizeReception();
        try {
            $callback();
        } catch (DomainException $exception) {
            return back()->withErrors(['queue' => $exception->getMessage()]);
        }
        return back()->with('success', 'Đã cập nhật phiếu hàng đợi.');
    }

    private function authorizeReception(): void
    {
        abort_unless(Auth::user()?->hasRole([User::ROLE_RECEPTIONIST, User::ROLE_NURSE, User::ROLE_ADMIN]), 403);
    }
}
