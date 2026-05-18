<?php

namespace App\Http\Controllers;

use App\Models\HealthLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HealthLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $healthLogs = HealthLog::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('health-logs.index', compact('healthLogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('health-logs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'log_date' => 'nullable|date|before_or_equal:now',
            'heart_rate' => 'nullable|integer|min:40|max:200',
            'sleep_hours' => 'nullable|numeric|min:0|max:24',
            'water_intake' => 'nullable|integer|min:0|max:10000',
            'calories' => 'nullable|integer|min:0|max:10000',
            'symptoms' => 'nullable|string',
            'mood' => 'nullable|string',
        ]);

        HealthLog::create([
            'user_id' => Auth::id(),
            'log_date' => $request->log_date ?: now(),
            'heart_rate' => $request->heart_rate,
            'sleep_hours' => $request->sleep_hours,
            'water_intake' => $request->water_intake,
            'calories' => $request->calories,
            'symptoms' => $request->symptoms,
            'mood' => $request->mood,
        ]);

        return redirect()
            ->route('health-logs.index')
            ->with('success', __('Health log created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(HealthLog $healthLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HealthLog $healthLog)
    {
        abort_unless($healthLog->user_id === Auth::id(), 403);

        return view('health-logs.edit', compact('healthLog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HealthLog $healthLog)
    {
        abort_unless($healthLog->user_id === Auth::id(), 403);

        $request->validate([
            'log_date' => 'nullable|date|before_or_equal:now',
            'heart_rate' => 'nullable|integer|min:40|max:200',
            'sleep_hours' => 'nullable|numeric|min:0|max:24',
            'water_intake' => 'nullable|integer|min:0|max:10000',
            'calories' => 'nullable|integer|min:0|max:10000',
            'symptoms' => 'nullable|string',
            'mood' => 'nullable|string',
        ]);

        $data = $request->all();
        if (empty($data['log_date'])) {
            $data['log_date'] = $healthLog->log_date ?: now();
        }

        $healthLog->update($data);

        return redirect()
            ->route('health-logs.index')
            ->with('success', __('Health log updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HealthLog $healthLog)
    {
        abort_unless($healthLog->user_id === Auth::id(), 403);

        $healthLog->delete();

        return redirect()
            ->route('health-logs.index')
            ->with('success', __('Health log deleted successfully.'));
    }
}
