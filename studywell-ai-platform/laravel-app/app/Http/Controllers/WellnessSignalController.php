<?php

namespace App\Http\Controllers;

use App\Models\WellnessSignal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WellnessSignalController extends Controller
{
    public function index(): View
    {
        return view('wellness-signals.index', [
            'signals' => WellnessSignal::query()->latest('recorded_on')->paginate(10),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        WellnessSignal::create($request->validate([
            'recorded_on' => ['required', 'date'],
            'focus_minutes' => ['required', 'integer', 'between:0,1440'],
            'sleep_hours' => ['required', 'numeric', 'between:0,24'],
            'mood_score' => ['required', 'integer', 'between:1,10'],
            'water_cups' => ['nullable', 'integer', 'between:0,30'],
            'screen_time_minutes' => ['nullable', 'integer', 'between:0,1440'],
            'energy_level' => ['nullable', 'integer', 'between:1,10'],
            'reflection' => ['nullable', 'string', 'max:1200'],
        ]));

        return back()->with('status', 'Wellness signal saved.');
    }

    public function destroy(WellnessSignal $wellnessSignal): RedirectResponse
    {
        $wellnessSignal->delete();

        return back()->with('status', 'Wellness signal deleted.');
    }
}
