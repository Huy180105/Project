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
            'heart_rate' => 'nullable|integer',
            'sleep_hours' => 'nullable|integer',
            'water_intake' => 'nullable|integer',
            'calories' => 'nullable|integer',
            'symptoms' => 'nullable|string',
            'mood' => 'nullable|string',
        ]);

        HealthLog::create([
            'user_id' => Auth::id(),
            'heart_rate' => $request->heart_rate,
            'sleep_hours' => $request->sleep_hours,
            'water_intake' => $request->water_intake,
            'calories' => $request->calories,
            'symptoms' => $request->symptoms,
            'mood' => $request->mood,
        ]);

        return redirect()
            ->route('health-logs.index')
            ->with('success', 'Health log created successfully.');
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
        return view('health-logs.edit', compact('healthLog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HealthLog $healthLog)
    {
        $request->validate([
            'heart_rate' => 'nullable|integer',
            'sleep_hours' => 'nullable|integer',
            'water_intake' => 'nullable|integer',
            'calories' => 'nullable|integer',
            'symptoms' => 'nullable|string',
            'mood' => 'nullable|string',
        ]);

        $healthLog->update($request->all());

        return redirect()
            ->route('health-logs.index')
            ->with('success', 'Health log updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HealthLog $healthLog)
    {
        $healthLog->delete();

        return redirect()
            ->route('health-logs.index')
            ->with('success', 'Health log deleted successfully.');
    }
}