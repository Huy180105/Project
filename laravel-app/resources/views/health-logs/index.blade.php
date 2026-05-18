<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.22em] text-cyan-600">{{ __('Health Logs') }}</p>
                <h1 class="mt-2 text-3xl font-black text-slate-950">{{ __('Health History') }}</h1>
                <p class="mt-2 text-sm font-medium text-slate-600">{{ __('Track recorded data so AI can analyze trends better.') }}</p>
            </div>
            <a href="{{ route('health-logs.create') }}" class="btn-primary">{{ __('Add Health Log') }}</a>
        </div>
    </x-slot>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($healthLogs as $log)
            <article class="rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.08)] backdrop-blur-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-600">{{ $log->log_date ? \Carbon\Carbon::parse($log->log_date)->format('d/m/Y H:i') : $log->created_at->format('d/m/Y H:i') }}</p>
                        <h2 class="mt-2 text-xl font-black text-slate-950">{{ $log->mood ?: __('Health log') }}</h2>
                    </div>
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-cyan-400 to-violet-500 text-white">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19.5 12.6 12 20l-7.5-7.4A5 5 0 1 1 12 6a5 5 0 1 1 7.5 6.6Z"/></svg>
                    </div>
                </div>
                <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                    <p class="rounded-2xl bg-rose-50 p-3 font-bold text-rose-700">{{ __('Heart') }}: {{ $log->heart_rate ?? '--' }}</p>
                    <p class="rounded-2xl bg-cyan-50 p-3 font-bold text-cyan-700">{{ __('Sleep') }}: {{ $log->sleep_hours ?? '--' }}h</p>
                    <p class="rounded-2xl bg-blue-50 p-3 font-bold text-blue-700">{{ __('Water') }}: {{ $log->water_intake ?? '--' }}</p>
                    <p class="rounded-2xl bg-violet-50 p-3 font-bold text-violet-700">{{ __('Calories') }}: {{ $log->calories ?? '--' }}</p>
                </div>
                @if($log->symptoms)
                    <p class="mt-4 rounded-2xl bg-slate-50 p-3 text-sm font-medium leading-6 text-slate-600">{{ $log->symptoms }}</p>
                @endif
                <div class="mt-5 flex gap-2">
                    <a href="{{ route('health-logs.edit', $log->id) }}" class="btn-secondary flex-1">{{ __('Edit') }}</a>
                    <form action="{{ route('health-logs.destroy', $log->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-2xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-black text-rose-600">{{ __('Delete') }}</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-[2rem] border border-dashed border-slate-200 bg-white/80 p-8 text-center md:col-span-2 xl:col-span-3">
                <p class="text-lg font-black text-slate-950">{{ __('No health logs yet') }}</p>
                <p class="mt-2 text-sm font-medium text-slate-600">{{ __('Click Add Health Log to start recording your health data.') }}</p>
                <a href="{{ route('health-logs.create') }}" class="btn-primary mt-5">{{ __('Add Health Log') }}</a>
            </div>
        @endforelse
    </section>
</x-app-layout>
