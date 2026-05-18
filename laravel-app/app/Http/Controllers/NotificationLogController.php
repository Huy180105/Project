<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use Illuminate\View\View;

class NotificationLogController extends Controller
{
    public function __invoke(): View
    {
        return view('notifications.index', [
            'notifications' => NotificationLog::query()
                ->with('queueTicket')
                ->latest()
                ->paginate(20),
        ]);
    }
}
