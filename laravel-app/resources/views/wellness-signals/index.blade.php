<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Wellness Signals</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8 xl:grid-cols-[360px_1fr]">
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-base font-semibold">New signal</h3>
                <form class="mt-4 space-y-4" method="POST" action="{{ route('wellness-signals.store') }}">
                    @csrf
                    <label class="block text-sm font-medium">
                        Date
                        <input class="field mt-1" type="date" name="recorded_on" value="{{ now()->toDateString() }}" required>
                    </label>
                    <label class="block text-sm font-medium">
                        Focus minutes
                        <input class="field mt-1" type="number" min="0" max="1440" name="focus_minutes" required>
                    </label>
                    <label class="block text-sm font-medium">
                        Sleep hours
                        <input class="field mt-1" type="number" step="0.1" min="0" max="24" name="sleep_hours" required>
                    </label>
                    <label class="block text-sm font-medium">
                        Mood score
                        <input class="field mt-1" type="number" min="1" max="10" name="mood_score" required>
                    </label>
                    <label class="block text-sm font-medium">
                        Water cups
                        <input class="field mt-1" type="number" min="0" max="30" name="water_cups">
                    </label>
                    <label class="block text-sm font-medium">
                        Screen time minutes
                        <input class="field mt-1" type="number" min="0" max="1440" name="screen_time_minutes">
                    </label>
                    <label class="block text-sm font-medium">
                        Energy level
                        <input class="field mt-1" type="number" min="1" max="10" name="energy_level">
                    </label>
                    <label class="block text-sm font-medium">
                        Reflection
                        <textarea class="field mt-1" name="reflection" rows="3"></textarea>
                    </label>
                    <button class="btn-primary w-full" type="submit">Save signal</button>
                </form>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h3 class="text-base font-semibold">Recent signals</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Date</th>
                                <th class="px-5 py-3">Focus</th>
                                <th class="px-5 py-3">Sleep</th>
                                <th class="px-5 py-3">Water</th>
                                <th class="px-5 py-3">Mood</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($signals as $signal)
                                <tr>
                                    <td class="px-5 py-3 font-medium">{{ $signal->recorded_on->toDateString() }}</td>
                                    <td class="px-5 py-3">{{ $signal->focus_minutes }}m</td>
                                    <td class="px-5 py-3">{{ $signal->sleep_hours }}h</td>
                                    <td class="px-5 py-3">{{ $signal->water_cups ?? '-' }}</td>
                                    <td class="px-5 py-3">{{ $signal->mood_score }}/10</td>
                                    <td class="px-5 py-3 text-right">
                                        <form method="POST" action="{{ route('wellness-signals.destroy', $signal) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:text-red-700" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-5 py-8 text-center text-slate-500" colspan="6">No wellness signals yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4">
                    {{ $signals->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
