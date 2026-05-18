<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-cyan-200 bg-cyan-50 px-4 py-2 text-xs font-black uppercase tracking-[0.24em] text-cyan-700">
                    <span class="h-2.5 w-2.5 rounded-full bg-cyan-500"></span>
                    Smart Queue Intake
                </div>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Tạo số thứ tự mới</h1>
                <p class="mt-3 max-w-2xl text-base font-medium leading-7 text-slate-600">Nhập thông tin bệnh nhân để đưa vào hàng đợi thông minh. Ca cấp cứu hoặc người ưu tiên có thể được chèn minh bạch.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 shadow-sm transition hover:border-cyan-300 hover:text-cyan-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
                    Trang chủ
                </a>
                <a href="{{ route('queue-tickets.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:bg-slate-800">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                    Quay về hàng đợi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <form action="{{ route('queue-tickets.store') }}" method="POST" class="grid gap-6">
                @csrf

                <label class="block">
                    <span class="text-sm font-black text-slate-700">Tên bệnh nhân</span>
                    <input type="text" name="patient_name" value="{{ old('patient_name') }}" class="field mt-2 w-full" placeholder="Ví dụ: Nguyễn Văn A" required>
                    @error('patient_name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </label>

                <div class="grid gap-6 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-black text-slate-700">Kênh lấy số</span>
                        <select name="channel" class="field mt-2 w-full" required>
                            @foreach($channelLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('channel') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('channel')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-black text-slate-700">Loại dịch vụ</span>
                        <select name="service_type" class="field mt-2 w-full" required>
                            @foreach($services as $service)
                                <option value="{{ $service }}" @selected(old('service_type') === $service)>{{ $service }}</option>
                            @endforeach
                        </select>
                        @error('service_type')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </label>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-black text-slate-700">Khoa khám</span>
                        <select name="department" class="field mt-2 w-full" required>
                            @foreach($departments as $department)
                                <option value="{{ $department }}" @selected(old('department') === $department)>{{ $department }}</option>
                            @endforeach
                        </select>
                        @error('department')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-black text-slate-700">Trạng thái thanh toán / HIS</span>
                        <select name="payment_status" class="field mt-2 w-full" required>
                            @foreach($paymentStatusLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('payment_status', \App\Models\QueueTicket::PAYMENT_PENDING) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('payment_status')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </label>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-black text-slate-700">Lý do ưu tiên</span>
                        <select name="priority_reason" class="field mt-2 w-full" required>
                            @foreach($priorityReasons as $value => $label)
                                <option value="{{ $value }}" @selected(old('priority_reason', 'normal') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('priority_reason')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-black text-slate-700">Mức ưu tiên</span>
                        <select name="priority_level" class="field mt-2 w-full" required>
                            @foreach($priorityLevels as $value => $label)
                                <option value="{{ $value }}" @selected((int) old('priority_level', 0) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('priority_level')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </label>
                </div>

                <label class="block">
                    <span class="text-sm font-black text-slate-700">Ghi chú điều phối</span>
                    <textarea name="notes" rows="4" class="field mt-2 w-full" placeholder="Ví dụ: bệnh nhân lớn tuổi, đi một mình, cần hỗ trợ di chuyển.">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </label>

                <label class="flex items-start gap-3 rounded-2xl bg-rose-50 p-4">
                    <input type="checkbox" name="emergency" value="1" class="mt-1 rounded border-rose-300 text-rose-600 focus:ring-rose-500" @checked(old('emergency'))>
                    <span>
                        <span class="block text-sm font-black text-rose-800">Chèn ca cấp cứu</span>
                        <span class="mt-1 block text-sm font-medium leading-6 text-rose-700">Tự động đưa vào khoa Cấp cứu, miễn chờ thanh toán và đặt mức ưu tiên cao nhất.</span>
                    </span>
                </label>

                <div class="flex flex-col gap-4 sm:flex-row sm:justify-end">
                    <a href="{{ route('queue-tickets.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 shadow-sm hover:bg-slate-50">Hủy</a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-500 px-5 py-3 text-sm font-black text-white shadow-sm hover:from-cyan-600 hover:to-blue-600">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                        Lưu số thứ tự
                    </button>
                </div>
            </form>
        </div>

        <aside class="space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black text-slate-950">Quy tắc điều phối</h2>
                <div class="mt-5 space-y-4">
                    <div class="rounded-2xl bg-cyan-50 p-4 text-sm font-medium leading-7 text-cyan-900">Theo Luật Khám bệnh, chữa bệnh 2023, người bệnh cấp cứu, trẻ dưới 6 tuổi, phụ nữ có thai, người khuyết tật nặng/đặc biệt nặng, người từ đủ 75 tuổi trở lên và người có công được ưu tiên khám.</div>
                    <div class="rounded-2xl bg-violet-50 p-4 text-sm font-medium leading-7 text-violet-900">Mức ưu tiên phải khớp với lý do ưu tiên. Nếu chọn ưu tiên nhưng không ghi chú căn cứ, hệ thống sẽ báo lỗi.</div>
                    <div class="rounded-2xl bg-rose-50 p-4 text-sm font-medium leading-7 text-rose-900">Ca cấp cứu luôn là mức 5, không được chờ xác nhận thanh toán trước khi gọi khám.</div>
                </div>
            </div>
        </aside>
    </div>
</x-app-layout>
