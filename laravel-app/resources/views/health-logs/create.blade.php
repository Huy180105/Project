<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.22em] text-cyan-600">{{ __('Health Logs') }}</p>
                <h1 class="mt-2 text-3xl font-black text-slate-950">{{ __('Add Health Log') }}</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-secondary">{{ __('Back to Dashboard') }}</a>
        </div>
    </x-slot>

    <section class="mx-auto max-w-3xl rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-[0_24px_80px_rgba(15,23,42,0.10)] backdrop-blur-2xl">
        <form action="{{ route('health-logs.store') }}" method="POST" class="grid gap-4 sm:grid-cols-2">
            @csrf

            <label class="block text-sm font-black text-slate-700 sm:col-span-2">
                {{ __('Recorded Date & Time') }}
                <input class="field mt-2 @error('log_date') border-rose-500 @enderror" type="datetime-local" name="log_date" value="{{ old('log_date', date('Y-m-d\TH:i')) }}">
                @error('log_date')
                    <span class="mt-1 block text-sm font-medium text-rose-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm font-black text-slate-700">
                {{ __('Heart Rate') }}
                <input class="field mt-2 @error('heart_rate') border-rose-500 @enderror" type="number" name="heart_rate" value="{{ old('heart_rate') }}" placeholder="82">
                @error('heart_rate')
                    <span class="mt-1 block text-sm font-medium text-rose-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm font-black text-slate-700">
                {{ __('Sleep Hours') }}
                <input class="field mt-2 @error('sleep_hours') border-rose-500 @enderror" type="number" name="sleep_hours" value="{{ old('sleep_hours') }}" placeholder="7" min="0" max="24">
                @error('sleep_hours')
                    <span class="mt-1 block text-sm font-medium text-rose-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm font-black text-slate-700">
                {{ __('Water Intake') }}
                <input class="field mt-2 @error('water_intake') border-rose-500 @enderror" type="number" name="water_intake" value="{{ old('water_intake') }}" placeholder="2000">
                @error('water_intake')
                    <span class="mt-1 block text-sm font-medium text-rose-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm font-black text-slate-700">
                {{ __('Calories') }}
                <input class="field mt-2 @error('calories') border-rose-500 @enderror" type="number" name="calories" value="{{ old('calories') }}" placeholder="1800">
                @error('calories')
                    <span class="mt-1 block text-sm font-medium text-rose-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm font-black text-slate-700 sm:col-span-2">
                {{ __('Symptoms') }}
                <textarea class="field mt-2 @error('symptoms') border-rose-500 @enderror" name="symptoms" rows="4" placeholder="{{ __('Example: sore throat, tired, slight fever...') }}">{{ old('symptoms') }}</textarea>
                @error('symptoms')
                    <span class="mt-1 block text-sm font-medium text-rose-600">{{ $message }}</span>
                @enderror
            </label>
            <label class="block text-sm font-black text-slate-700 sm:col-span-2">
                {{ __('Mood') }}
                <input class="field mt-2 @error('mood') border-rose-500 @enderror" type="text" name="mood" value="{{ old('mood') }}" placeholder="{{ __('Fine, tired, stressed...') }}">
                @error('mood')
                    <span class="mt-1 block text-sm font-medium text-rose-600">{{ $message }}</span>
                @enderror
            </label>

            <div class="flex flex-col-reverse gap-3 pt-2 sm:col-span-2 sm:flex-row sm:justify-end">
                <a href="{{ route('health-logs.index') }}" class="btn-secondary flex items-center justify-center">{{ __('Cancel') }}</a>
                <button type="submit" class="btn-primary">{{ __('Save Health Log') }}</button>
            </div>
        </form>
    </section>
</x-app-layout>
