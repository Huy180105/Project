<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Phiếu khám {{ $ticket->displayNumber() }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .ticket-card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-sky-50 text-slate-950">
    <main class="mx-auto flex min-h-screen max-w-4xl flex-col justify-center px-6 py-8">
        <section class="ticket-card rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
            <div class="flex flex-col gap-6 border-b border-slate-200 pb-6 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.28em] text-cyan-700">Phiếu khám bệnh</p>
                    <h1 class="mt-3 text-7xl font-black tracking-tight sm:text-8xl">{{ $ticket->displayNumber() }}</h1>
                    <p class="mt-4 text-xl font-bold">{{ $ticket->patient_name }}</p>
                </div>

                <div class="grid h-40 w-40 place-items-center rounded-3xl border-2 border-dashed border-slate-300 bg-slate-50 text-center text-sm font-bold text-slate-500">
                    QR theo dõi số
                </div>
            </div>

            <div class="grid gap-4 py-6 sm:grid-cols-2">
                <div class="rounded-3xl bg-sky-50 p-5">
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500">Khoa khám</p>
                    <p class="mt-2 text-2xl font-black">{{ $ticket->department }}</p>
                </div>
                <div class="rounded-3xl bg-sky-50 p-5">
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500">Phòng</p>
                    <p class="mt-2 text-2xl font-black">{{ $room }}</p>
                </div>
                <div class="rounded-3xl bg-sky-50 p-5">
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500">Thời gian chờ dự kiến</p>
                    <p class="mt-2 text-2xl font-black">{{ $ticket->estimated_wait }} phút</p>
                </div>
                <div class="rounded-3xl bg-sky-50 p-5">
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500">Trạng thái</p>
                    <p class="mt-2 text-2xl font-black">{{ \App\Models\QueueTicket::statusLabels()[$ticket->status] }}</p>
                </div>
            </div>

            <p class="rounded-3xl bg-amber-50 p-5 text-base leading-7 text-amber-950">
                Vui lòng giữ phiếu này. Khi đến gần lượt, hãy theo dõi màn hình hàng đợi của khoa hoặc hỏi nhân viên hỗ trợ nếu cần.
            </p>
        </section>

        <div class="no-print mt-6 grid gap-3 sm:grid-cols-3">
            <a href="{{ route('kiosk.index') }}" class="rounded-3xl border border-slate-200 bg-white px-5 py-4 text-center text-lg font-black">Lấy số khác</a>
            <a href="{{ route('display.department', $ticket->department) }}" class="rounded-3xl bg-cyan-700 px-5 py-4 text-center text-lg font-black text-white">Xem màn hình hàng đợi</a>
            <button onclick="window.print()" class="rounded-3xl bg-slate-950 px-5 py-4 text-lg font-black text-white">In phiếu</button>
        </div>
    </main>
</body>
</html>
