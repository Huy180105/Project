<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-black uppercase tracking-[0.24em] text-blue-700">
                    <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                    Điều phối hàng đợi
                </div>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Bảng điều phối hàng đợi bệnh viện</h1>
                <p class="mt-3 max-w-2xl text-base font-medium leading-7 text-slate-600">
                    Gọi lượt, xác nhận HIS, xử lý vắng mặt và hoàn tất khám theo đúng trạng thái vận hành.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 shadow-sm transition hover:border-cyan-300 hover:text-cyan-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                    Quay lại dashboard
                </a>
                <a href="{{ route('queue-tickets.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-500 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:-translate-y-0.5">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Tạo số mới
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        @if(session('success'))
            <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->has('queue'))
            <div class="rounded-3xl border border-rose-200 bg-rose-50 px-6 py-4 text-sm font-bold text-rose-800">
                {{ $errors->first('queue') }}
            </div>
        @endif

        <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-5">
            <div class="metric-card">
                <p class="text-sm font-bold text-slate-500">Chờ thanh toán</p>
                <h3 class="mt-4 text-4xl font-black text-slate-950">{{ $waiting }}</h3>
            </div>
            <div class="metric-card">
                <p class="text-sm font-bold text-slate-500">Sẵn sàng gọi</p>
                <h3 class="mt-4 text-4xl font-black text-slate-950">{{ $ready }}</h3>
            </div>
            <div class="metric-card">
                <p class="text-sm font-bold text-slate-500">Đang gọi</p>
                <h3 class="mt-4 text-4xl font-black text-slate-950">{{ $calling }}</h3>
            </div>
            <div class="metric-card">
                <p class="text-sm font-bold text-slate-500">Vắng mặt</p>
                <h3 class="mt-4 text-4xl font-black text-slate-950">{{ $missed }}</h3>
            </div>
            <div class="metric-card">
                <p class="text-sm font-bold text-slate-500">Hoàn thành hôm nay</p>
                <h3 class="mt-4 text-4xl font-black text-slate-950">{{ $completed }}</h3>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex flex-col gap-3 border-b border-slate-100 pb-5 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <h2 class="text-lg font-black text-slate-950">Gọi lượt theo khoa</h2>
                    <p class="mt-1 text-sm text-slate-500">Chỉ gọi các phiếu đã sẵn sàng sau xác nhận HIS.</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($departments as $department)
                        <form method="POST" action="{{ route('queue-tickets.call-next', ['department' => $department]) }}">
                            @csrf
                            <button class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2.5 text-sm font-black text-cyan-900 transition hover:bg-cyan-100" type="submit">
                                <span>{{ $department }}</span>
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>

            <form method="GET" action="{{ route('queue-tickets.index') }}" class="grid gap-4 xl:grid-cols-[1fr_1fr_1fr_auto] xl:items-end">
                <label>
                    <span class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Khoa</span>
                    <select name="department" class="field mt-2">
                        <option value="">Tất cả khoa</option>
                        @foreach($departments as $department)
                            <option value="{{ $department }}" @selected(($filters['department'] ?? null) === $department)>{{ $department }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Mức ưu tiên</span>
                    <select name="priority_level" class="field mt-2">
                        <option value="">Tất cả mức</option>
                        @foreach($priorityLevels as $value => $label)
                            <option value="{{ $value }}" @selected((string) ($filters['priority_level'] ?? '') === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Trạng thái HIS</span>
                    <select name="payment_status" class="field mt-2">
                        <option value="">Tất cả trạng thái</option>
                        @foreach($paymentStatusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['payment_status'] ?? null) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="flex flex-wrap gap-3">
                    <button class="inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-slate-800" type="submit">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Lọc danh sách
                    </button>
                    <a href="{{ route('queue-tickets.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:border-cyan-300 hover:text-cyan-700">
                        Xóa lọc
                    </a>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5">
                <h2 class="text-2xl font-black text-slate-950">Danh sách hàng đợi</h2>
                <p class="mt-1 text-sm text-slate-500">Ưu tiên cấp cứu, sau đó mức ưu tiên cao hơn, rồi đến phiếu tạo sớm hơn.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[1280px] table-fixed text-left text-sm text-slate-700">
                    <colgroup>
                        <col class="w-[90px]">
                        <col class="w-[220px]">
                        <col class="w-[130px]">
                        <col class="w-[135px]">
                        <col class="w-[190px]">
                        <col class="w-[140px]">
                        <col class="w-[170px]">
                        <col class="w-[250px]">
                    </colgroup>
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Số</th>
                            <th class="px-4 py-3">Bệnh nhân</th>
                            <th class="px-4 py-3">Khoa</th>
                            <th class="px-4 py-3">Kênh</th>
                            <th class="px-4 py-3">HIS</th>
                            <th class="px-4 py-3">Trạng thái</th>
                            <th class="px-4 py-3">Ưu tiên</th>
                            <th class="px-4 py-3">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tickets as $ticket)
                            @php
                                $statusClass = match ($ticket->status) {
                                    \App\Models\QueueTicket::STATUS_WAITING_PAYMENT => 'bg-amber-50 text-amber-700',
                                    \App\Models\QueueTicket::STATUS_READY => 'bg-cyan-50 text-cyan-700',
                                    \App\Models\QueueTicket::STATUS_CALLING => 'bg-blue-50 text-blue-700',
                                    \App\Models\QueueTicket::STATUS_SERVING => 'bg-violet-50 text-violet-700',
                                    \App\Models\QueueTicket::STATUS_MISSED => 'bg-rose-50 text-rose-700',
                                    \App\Models\QueueTicket::STATUS_COMPLETED => 'bg-emerald-50 text-emerald-700',
                                    \App\Models\QueueTicket::STATUS_CANCELLED => 'bg-slate-100 text-slate-600',
                                    default => 'bg-slate-100 text-slate-600',
                                };
                            @endphp
                            <tr class="align-middle">
                                <td class="px-4 py-4 text-lg font-black text-slate-950">{{ $ticket->displayNumber() }}</td>
                                <td class="px-4 py-4">
                                    <p class="font-bold text-slate-950">{{ $ticket->patient_name }}</p>
                                    <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">{{ $ticket->notes ?: 'Không có ghi chú' }}</p>
                                </td>
                                <td class="px-4 py-4 font-semibold text-slate-800">{{ $ticket->department }}</td>
                                <td class="px-4 py-4">{{ \App\Models\QueueTicket::channelLabels()[$ticket->channel] ?? $ticket->channel }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-black {{ $ticket->payment_status === \App\Models\QueueTicket::PAYMENT_PENDING ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ \App\Models\QueueTicket::paymentStatusLabels()[$ticket->payment_status] ?? $ticket->payment_status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">
                                        {{ \App\Models\QueueTicket::statusLabels()[$ticket->status] ?? $ticket->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex whitespace-nowrap rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">
                                        {{ \App\Models\QueueTicket::priorityLevels()[$ticket->priority_level] ?? $ticket->priority_level }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="grid min-w-[220px] grid-cols-2 gap-2">
                                        @if($ticket->status === \App\Models\QueueTicket::STATUS_WAITING_PAYMENT)
                                            <form method="POST" action="{{ route('queue-tickets.payment', $ticket) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="action-pill w-full justify-center bg-emerald-600 text-white hover:bg-emerald-700">Xác nhận HIS</button>
                                            </form>
                                        @endif

                                        @if($ticket->status === \App\Models\QueueTicket::STATUS_CALLING)
                                            <form method="POST" action="{{ route('queue-tickets.serving', $ticket) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="action-pill w-full justify-center bg-blue-600 text-white hover:bg-blue-700">Bắt đầu khám</button>
                                            </form>
                                            <form method="POST" action="{{ route('queue-tickets.missed', $ticket) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="action-pill w-full justify-center bg-rose-500 text-white hover:bg-rose-600">Vắng mặt</button>
                                            </form>
                                        @endif

                                        @if($ticket->status === \App\Models\QueueTicket::STATUS_SERVING)
                                            <form method="POST" action="{{ route('queue-tickets.complete', $ticket) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="action-pill w-full justify-center bg-emerald-600 text-white hover:bg-emerald-700">Hoàn thành</button>
                                            </form>
                                        @endif

                                        @if($ticket->status === \App\Models\QueueTicket::STATUS_MISSED)
                                            <form method="POST" action="{{ route('queue-tickets.recall', $ticket) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="action-pill w-full justify-center bg-slate-800 text-white hover:bg-slate-900">Gọi lại</button>
                                            </form>
                                        @endif

                                        @if(! $ticket->isTerminal())
                                            <form method="POST" action="{{ route('queue-tickets.cancel', $ticket) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="action-pill w-full justify-center border border-slate-200 bg-white text-slate-700 hover:border-rose-300 hover:text-rose-700">Hủy phiếu</button>
                                            </form>
                                        @endif

                                        @if($ticket->isTerminal() || $ticket->status === \App\Models\QueueTicket::STATUS_READY)
                                            <span class="inline-flex w-full items-center justify-center whitespace-nowrap rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600">
                                                {{ $ticket->status === \App\Models\QueueTicket::STATUS_READY ? 'Chờ gọi theo khoa' : 'Đã kết thúc' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">Chưa có số thứ tự phù hợp bộ lọc hiện tại.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
