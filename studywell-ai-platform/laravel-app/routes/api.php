<?php

use App\Models\WellnessSignal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/wellness-signals', fn () => WellnessSignal::latest('recorded_on')->limit(30)->get());

Route::post('/wellness-signals', function (Request $request) {
    $data = $request->validate([
        'recorded_on' => ['required', 'date'],
        'focus_minutes' => ['required', 'integer', 'between:0,1440'],
        'sleep_hours' => ['required', 'numeric', 'between:0,24'],
        'mood_score' => ['required', 'integer', 'between:1,10'],
        'water_cups' => ['nullable', 'integer', 'between:0,30'],
        'screen_time_minutes' => ['nullable', 'integer', 'between:0,1440'],
        'energy_level' => ['nullable', 'integer', 'between:1,10'],
        'reflection' => ['nullable', 'string', 'max:1200'],
    ]);

    return WellnessSignal::create($data);
});
