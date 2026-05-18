<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-4 py-2 text-xs font-black uppercase tracking-[0.24em] text-violet-700">
                    <span class="h-2.5 w-2.5 rounded-full bg-violet-500"></span>
                    Bảng gọi khám
                </div>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Điều phối khám theo khoa</h1>
                <p class="mt-3 max-w-2xl text-base font-medium leading-7 text-slate-600">
                    Màn hình thao tác nhanh cho bác sĩ và điều dưỡng, tập trung vào gọi bệnh nhân, bắt đầu khám và kết thúc lượt khám.
                </p>
            </div>

            <form method="GET" action="{{ route('doctor.queue.index') }}" class="w-full max-w-sm">
                <label>
                    <span class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Khoa đang phụ trách</span>
                    <select name="department" class="field mt-2" onchange="this.form.submit()">
                        @foreach($departments as $item)
                            <option value="{{ $item }}" @selected($department === $item)>{{ $item }}</option>
                        @endforeach
                    </select>
                </label>
            </form>
        </div>
    </x-slot>

    <div class="space-y-8">
        @if(session('success'))
            <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>
        @endif

        @if($errors->has('queue'))
            <div class="rounded-3xl border border-rose-200 bg-rose-50 px-6 py-4 text-sm font-bold text-rose-800">{{ $errors->first('queue') }}</div>
        @endif

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Bệnh nhân đang gọi</p>
                <div class="mt-4 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-5xl font-black text-slate-950">{{ $callingTicket?->displayNumber() ?? '---' }}</p>
                        <p class="mt-3 text-lg font-bold text-slate-900">{{ $callingTicket?->patient_name ?? 'Chưa có bệnh nhân đang gọi' }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $callingTicket ? \App\Models\QueueTicket::roomForService($callingTicket->service_type, $callingTicket->department) : 'Sẵn sàng nhận lượt tiếp theo' }}</p>
                    </div>
                    @if($callingTicket)
                        <div class="flex flex-wrap gap-3">
                            <form method="POST" action="{{ route('doctor.queue.serving', $callingTicket) }}">@csrf @method('PATCH')<button class="btn-primary px-5 py-3">Bắt đầu khám</button></form>
                            <form method="POST" action="{{ route('doctor.queue.missed', $callingTicket) }}">@csrf @method('PATCH')<button class="btn-secondary px-5 py-3">Vắng mặt</button></form>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Bệnh nhân đang khám</p>
                <div class="mt-4 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-5xl font-black text-slate-950">{{ $servingTicket?->displayNumber() ?? '---' }}</p>
                        <p class="mt-3 text-lg font-bold text-slate-900">{{ $servingTicket?->patient_name ?? 'Chưa có bệnh nhân đang khám' }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $servingTicket ? 'Đang xử lý chuyên môn' : 'Chờ bác sĩ nhận bệnh nhân' }}</p>
                    </div>
                    @if($servingTicket)
                        <form method="POST" action="{{ route('doctor.queue.complete', $servingTicket) }}">@csrf @method('PATCH')<button class="btn-accent px-5 py-3">Hoàn thành khám</button></form>
                    @endif
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-black text-slate-950">Hàng chờ sẵn sàng</h2>
                    <p class="mt-1 text-sm text-slate-500">Đã sắp theo cấp cứu, mức ưu tiên và thời điểm tạo phiếu.</p>
                </div>
                <form method="POST" action="{{ route('doctor.queue.call-next') }}">
                    @csrf
                    <input type="hidden" name="department" value="{{ $department }}">
                    <button class="inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-6 py-4 text-base font-black text-white transition hover:bg-slate-800">Gọi bệnh nhân tiếp theo</button>
                </form>
            </div>

            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-100">
                <table class="min-w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr><th class="px-4 py-3">Số</th><th class="px-4 py-3">Bệnh nhân</th><th class="px-4 py-3">Ưu tiên</th><th class="px-4 py-3">Thời gian chờ</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($readyTickets as $ticket)
                            <tr>
                                <td class="px-4 py-4 text-lg font-black text-slate-950">{{ $ticket->displayNumber() }}</td>
                                <td class="px-4 py-4"><p class="font-bold text-slate-950">{{ $ticket->patient_name }}</p><p class="mt-1 text-xs text-slate-500">{{ $ticket->notes ?: 'Không có ghi chú' }}</p></td>
                                <td class="px-4 py-4">{{ \App\Models\QueueTicket::priorityLevels()[$ticket->priority_level] ?? $ticket->priority_level }}</td>
                                <td class="px-4 py-4">{{ $ticket->estimated_wait }} phút</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">Chưa có bệnh nhân sẵn sàng gọi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black text-slate-950">Bệnh nhân vắng mặt</h2>
                <div class="mt-5 space-y-3">
                    @forelse($missedTickets as $ticket)
                        <div class="flex flex-col gap-3 rounded-2xl bg-rose-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div><p class="font-black text-slate-950">{{ $ticket->displayNumber() }} · {{ $ticket->patient_name }}</p><p class="mt-1 text-sm text-slate-600">Đánh dấu vắng lúc {{ $ticket->missed_at?->format('H:i') }}</p></div>
                            <form method="POST" action="{{ route('doctor.queue.recall', $ticket) }}">@csrf @method('PATCH')<button class="btn-secondary">Gọi lại</button></form>
                        </div>
                    @empty
                        <p class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">Không có bệnh nhân vắng mặt.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black text-slate-950">Đã hoàn thành hôm nay</h2>
                <div class="mt-5 space-y-3">
                    @forelse($completedTickets as $ticket)
                        <div class="rounded-2xl bg-emerald-50 p-4"><p class="font-black text-slate-950">{{ $ticket->displayNumber() }} · {{ $ticket->patient_name }}</p><p class="mt-1 text-sm text-slate-600">Hoàn thành lúc {{ $ticket->completed_at?->format('H:i') }}</p></div>
                    @empty
                        <p class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">Chưa có lượt khám hoàn thành hôm nay.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
