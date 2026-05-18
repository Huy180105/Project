<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-black uppercase tracking-[0.24em] text-emerald-700">
                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                Quầy tiếp nhận
            </div>
            <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Tiếp nhận và kích hoạt phiếu khám</h1>
            <p class="mt-3 max-w-3xl text-base font-medium leading-7 text-slate-600">Tạo phiếu, ghi nhận căn cứ ưu tiên và chuyển phiếu đã xác nhận HIS vào hàng sẵn sàng gọi.</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        @if(session('success'))<div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>@endif
        @if($errors->has('queue'))<div class="rounded-3xl border border-rose-200 bg-rose-50 px-6 py-4 text-sm font-bold text-rose-800">{{ $errors->first('queue') }}</div>@endif

        <section class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black text-slate-950">Tạo phiếu khám mới</h2>
                <form method="POST" action="{{ route('reception.queue.store') }}" class="mt-6 grid gap-5">
                    @csrf
                    <label><span class="text-sm font-black text-slate-700">Tên bệnh nhân</span><input name="patient_name" value="{{ old('patient_name') }}" class="field mt-2" required>@error('patient_name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror</label>
                    <div class="grid gap-5 md:grid-cols-2">
                        <label><span class="text-sm font-black text-slate-700">Khoa khám</span><select name="department" class="field mt-2">@foreach($departments as $department)<option value="{{ $department }}" @selected(old('department') === $department)>{{ $department }}</option>@endforeach</select></label>
                        <label><span class="text-sm font-black text-slate-700">Kênh lấy số</span><select name="channel" class="field mt-2">@foreach($channelLabels as $value => $label)<option value="{{ $value }}" @selected(old('channel') === $value)>{{ $label }}</option>@endforeach</select></label>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        <label><span class="text-sm font-black text-slate-700">Loại dịch vụ</span><select name="service_type" class="field mt-2">@foreach($services as $service)<option value="{{ $service }}" @selected(old('service_type') === $service)>{{ $service }}</option>@endforeach</select></label>
                        <label><span class="text-sm font-black text-slate-700">Trạng thái HIS/thanh toán</span><select name="payment_status" class="field mt-2">@foreach($paymentStatusLabels as $value => $label)<option value="{{ $value }}" @selected(old('payment_status', \App\Models\QueueTicket::PAYMENT_PENDING) === $value)>{{ $label }}</option>@endforeach</select>@error('payment_status')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror</label>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        <label><span class="text-sm font-black text-slate-700">Lý do ưu tiên</span><select name="priority_reason" class="field mt-2">@foreach($priorityReasons as $value => $label)<option value="{{ $value }}" @selected(old('priority_reason', 'normal') === $value)>{{ $label }}</option>@endforeach</select>@error('priority_reason')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror</label>
                        <label><span class="text-sm font-black text-slate-700">Mức ưu tiên</span><select name="priority_level" class="field mt-2">@foreach($priorityLevels as $value => $label)<option value="{{ $value }}" @selected((int) old('priority_level', 0) === $value)>{{ $label }}</option>@endforeach</select>@error('priority_level')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror</label>
                    </div>
                    <label><span class="text-sm font-black text-slate-700">Ghi chú căn cứ ưu tiên</span><textarea name="notes" rows="4" class="field mt-2">{{ old('notes') }}</textarea>@error('notes')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror</label>
                    <label class="flex items-start gap-3 rounded-2xl bg-rose-50 p-4"><input type="checkbox" name="emergency" value="1" class="mt-1 rounded border-rose-300 text-rose-600 focus:ring-rose-500" @checked(old('emergency'))><span><span class="block text-sm font-black text-rose-800">Ca cấp cứu</span><span class="mt-1 block text-sm text-rose-700">Tự chuyển sang mức ưu tiên 5 và không được chờ xác nhận thanh toán.</span></span></label>
                    <div class="flex justify-end"><button class="btn-accent px-5 py-3">Lưu phiếu khám</button></div>
                </form>
            </div>
            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-black text-slate-950">Chờ xác nhận HIS/thanh toán</h2>
                    <div class="mt-5 space-y-3">@forelse($waitingTickets as $ticket)<div class="rounded-2xl bg-amber-50 p-4"><div class="flex items-start justify-between gap-4"><div><p class="font-black text-slate-950">{{ $ticket->displayNumber() }} · {{ $ticket->patient_name }}</p><p class="mt-1 text-sm text-slate-600">{{ $ticket->department }}</p></div><div class="flex gap-2"><form method="POST" action="{{ route('reception.queue.activate', $ticket) }}">@csrf @method('PATCH')<button class="btn-accent">Kích hoạt</button></form><form method="POST" action="{{ route('reception.queue.cancel', $ticket) }}">@csrf @method('PATCH')<button class="btn-secondary">Hủy</button></form></div></div></div>@empty<p class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">Không có phiếu chờ xác nhận.</p>@endforelse</div>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"><h2 class="text-2xl font-black text-slate-950">Đã sẵn sàng</h2><div class="mt-5 space-y-3">@forelse($readyTickets as $ticket)<div class="rounded-2xl bg-cyan-50 p-4"><p class="font-black text-slate-950">{{ $ticket->displayNumber() }} · {{ $ticket->patient_name }}</p><p class="mt-1 text-sm text-slate-600">{{ $ticket->department }} · {{ \App\Models\QueueTicket::priorityLevels()[$ticket->priority_level] ?? $ticket->priority_level }}</p></div>@empty<p class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">Chưa có phiếu sẵn sàng.</p>@endforelse</div></div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"><h2 class="text-2xl font-black text-slate-950">Đã hủy hôm nay</h2><div class="mt-5 space-y-3">@forelse($cancelledTickets as $ticket)<div class="rounded-2xl bg-slate-50 p-4"><p class="font-black text-slate-950">{{ $ticket->displayNumber() }} · {{ $ticket->patient_name }}</p><p class="mt-1 text-sm text-slate-600">{{ $ticket->department }}</p></div>@empty<p class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">Chưa có phiếu bị hủy hôm nay.</p>@endforelse</div></div>
            </div>
        </section>
    </div>
</x-app-layout>
