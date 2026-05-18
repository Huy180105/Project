<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKioskTicketRequest;
use App\Models\QueueTicket;
use App\Services\QueueEngine;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class KioskController extends Controller
{
    public function index(): View
    {
        return view('kiosk.index', [
            'departments' => QueueTicket::departments(),
            'priorityReasons' => QueueTicket::priorityReasons(),
        ]);
    }

    public function store(StoreKioskTicketRequest $request, QueueEngine $queueEngine): RedirectResponse
    {
        $ticket = $queueEngine->createTicket($request->payload());

        return redirect()->route('kiosk.tickets.show', $ticket);
    }

    public function show(QueueTicket $ticket): View
    {
        abort_unless($ticket->channel === QueueTicket::CHANNEL_KIOSK, 404);

        return view('kiosk.show', [
            'ticket' => $ticket,
            'room' => QueueTicket::roomForService($ticket->service_type, $ticket->department),
        ]);
    }
}
