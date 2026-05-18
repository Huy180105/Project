<?php

namespace App\Http\Controllers;

use App\Models\QueueTicket;
use Illuminate\Contracts\View\View;

class DisplayController extends Controller
{
    public function __invoke(string $department): View
    {
        abort_unless(in_array($department, QueueTicket::departments(), true), 404);

        $callingTicket = QueueTicket::where('department', $department)
            ->where('status', QueueTicket::STATUS_CALLING)
            ->latest('called_at')
            ->first();

        $servingTicket = QueueTicket::where('department', $department)
            ->where('status', QueueTicket::STATUS_SERVING)
            ->latest('called_at')
            ->first();

        $nextTickets = QueueTicket::where('department', $department)
            ->where('status', QueueTicket::STATUS_READY)
            ->orderByDesc('priority_level')
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        $roomSource = $callingTicket ?? $servingTicket ?? $nextTickets->first();

        return view('display.queue', [
            'department' => $department,
            'callingTicket' => $callingTicket,
            'servingTicket' => $servingTicket,
            'nextTickets' => $nextTickets,
            'room' => $roomSource
                ? QueueTicket::roomForService($roomSource->service_type, $roomSource->department)
                : QueueTicket::roomForService(QueueTicket::SERVICE_DICH_VU, $department),
        ]);
    }
}
