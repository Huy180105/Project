<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kiosk lấy số khám</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-sky-50 text-slate-950">
    <main class="mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-6 py-8 lg:px-10">
        <header class="mb-8 max-w-3xl">
            <p class="text-sm font-black uppercase tracking-[0.28em] text-cyan-700">Kiosk tự phục vụ</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight sm:text-5xl">Lấy số khám bệnh</h1>
            <p class="mt-4 text-lg leading-8 text-slate-600">
                Chọn khoa cần khám và nhập thông tin cơ bản. Hệ thống sẽ in phiếu để bạn theo dõi lượt gọi.
            </p>
        </header>

        <form method="POST" action="{{ route('kiosk.tickets.store') }}" class="grid gap-6 rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm sm:p-8 lg:grid-cols-2">
            @csrf

            <label class="grid gap-3">
                <span class="text-base font-bold">Họ và tên người bệnh</span>
                <input name="patient_name" value="{{ old('patient_name') }}" required class="h-16 rounded-2xl border border-slate-200 px-5 text-xl outline-none transition focus:border-cyan-500" placeholder="Ví dụ: Nguyễn Văn An">
                @error('patient_name')<span class="text-sm font-semibold text-rose-600">{{ $message }}</span>@enderror
            </label>

            <label class="grid gap-3">
                <span class="text-base font-bold">Số điện thoại (không bắt buộc)</span>
                <input name="patient_phone" value="{{ old('patient_phone') }}" class="h-16 rounded-2xl border border-slate-200 px-5 text-xl outline-none transition focus:border-cyan-500" placeholder="Ví dụ: 0901 234 567">
                @error('patient_phone')<span class="text-sm font-semibold text-rose-600">{{ $message }}</span>@enderror
            </label>

            <label class="grid gap-3">
                <span class="text-base font-bold">Khoa khám</span>
                <select name="department" required class="h-16 rounded-2xl border border-slate-200 px-5 text-xl outline-none transition focus:border-cyan-500">
                    @foreach($departments as $department)
                        <option value="{{ $department }}" @selected(old('department') === $department)>{{ $department }}</option>
                    @endforeach
                </select>
                @error('department')<span class="text-sm font-semibold text-rose-600">{{ $message }}</span>@enderror
            </label>

            <label class="grid gap-3">
                <span class="text-base font-bold">Trường hợp ưu tiên</span>
                <select name="priority_reason" required class="h-16 rounded-2xl border border-slate-200 px-5 text-xl outline-none transition focus:border-cyan-500">
                    @foreach($priorityReasons as $value => $label)
                        <option value="{{ $value }}" @selected(old('priority_reason', 'normal') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('priority_reason')<span class="text-sm font-semibold text-rose-600">{{ $message }}</span>@enderror
            </label>

            <div class="rounded-3xl bg-cyan-50 p-5 text-base leading-7 text-cyan-950 lg:col-span-2">
                Nếu đang đau ngực, khó thở, chảy máu nhiều hoặc có dấu hiệu cấp cứu, hãy chọn đúng mục cấp cứu để được điều phối trước.
            </div>

            <button class="h-20 rounded-3xl bg-slate-950 text-2xl font-black text-white transition hover:bg-cyan-700 lg:col-span-2">
                Xác nhận lấy số
            </button>
        </form>
    </main>
</body>
</html>
