<?php

namespace App\Http\Controllers;

use App\Models\WellnessSignal;
use App\Services\GatewayClient;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(GatewayClient $gateway): View
    {
        $signals = WellnessSignal::query()
            ->latest('recorded_on')
            ->limit(14)
            ->get()
            ->reverse()
            ->values();

        $stats = [
            'avg_focus' => round((float) $signals->avg('focus_minutes')),
            'avg_sleep' => round((float) $signals->avg('sleep_hours'), 1),
            'avg_mood' => round((float) $signals->avg('mood_score'), 1),
            'avg_screen_time' => round((float) $signals->avg('screen_time_minutes')),
        ];

        $insight = $gateway->wellnessInsights($signals->toArray());

        return view('dashboard', [
            'signals' => $signals,
            'stats' => $stats,
            'insight' => $insight['summary'] ?? 'Add study-life signals to generate AI insights.',
        ]);
    }
}
