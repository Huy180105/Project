<?php

namespace App\Http\Controllers;

use App\Models\QueueTicket;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $callingTicket = QueueTicket::where('status', QueueTicket::STATUS_CALLING)->latest('called_at')->first();
        $nextTicket = QueueTicket::where('status', QueueTicket::STATUS_READY)
            ->orderByDesc('priority_level')
            ->orderBy('created_at')
            ->first();

        return view('dashboard', [
            'callingTicket' => $callingTicket,
            'nextTicket' => $nextTicket,
            'waitingCount' => QueueTicket::where('status', QueueTicket::STATUS_WAITING_PAYMENT)->count(),
            'readyCount' => QueueTicket::where('status', QueueTicket::STATUS_READY)->count(),
            'missedCount' => QueueTicket::where('status', QueueTicket::STATUS_MISSED)->count(),
            'completedTodayCount' => QueueTicket::where('status', QueueTicket::STATUS_COMPLETED)->whereDate('completed_at', now())->count(),
            'averageEstimatedWait' => round((float) QueueTicket::whereIn('status', [
                QueueTicket::STATUS_WAITING_PAYMENT,
                QueueTicket::STATUS_READY,
            ])->avg('estimated_wait'), 1),
            'hourlyLabels' => ['07:00', '08:00', '09:00', '10:00', '11:00', '13:00', '14:00'],
            'hourlyData' => [18, 42, 57, 49, 36, 44, 31],
        ]);
    }
}
