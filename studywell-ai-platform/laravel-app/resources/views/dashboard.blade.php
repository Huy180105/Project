@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid gap-4 md:grid-cols-4">
        <div class="metric-card">
            <p class="text-sm text-slate-500">Avg focus</p>
            <p class="mt-2 text-3xl font-bold">{{ $stats['avg_focus'] ?: '-' }}m</p>
        </div>
        <div class="metric-card">
            <p class="text-sm text-slate-500">Avg sleep</p>
            <p class="mt-2 text-3xl font-bold">{{ $stats['avg_sleep'] ?: '-' }}h</p>
        </div>
        <div class="metric-card">
            <p class="text-sm text-slate-500">Mood</p>
            <p class="mt-2 text-3xl font-bold">{{ $stats['avg_mood'] ?: '-' }}/10</p>
        </div>
        <div class="metric-card">
            <p class="text-sm text-slate-500">Screen time</p>
            <p class="mt-2 text-3xl font-bold">{{ $stats['avg_screen_time'] ?: '-' }}m</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[2fr_1fr]">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold">Study-life trends</h2>
                <a href="{{ route('wellness-signals.index') }}" class="btn-secondary">Add signal</a>
            </div>
            <div class="mt-4 h-80">
                <canvas id="studyChart" class="h-full w-full"></canvas>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold">AI study-life insight</h2>
            <p class="mt-3 text-sm leading-6 text-slate-700">{{ $insight }}</p>
            <a href="{{ route('ai-chat.index') }}" class="btn-primary mt-5 w-full">Ask AI</a>
        </section>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const signals = @json($signals);
            const labels = signals.map((signal) => signal.recorded_on);

            new window.Chart(document.getElementById('studyChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Focus minutes',
                            data: signals.map((signal) => signal.focus_minutes),
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.12)',
                            tension: 0.35,
                            fill: true,
                        },
                        {
                            label: 'Mood score',
                            data: signals.map((signal) => signal.mood_score),
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            tension: 0.35,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true },
                    },
                },
            });
        });
    </script>
@endsection
