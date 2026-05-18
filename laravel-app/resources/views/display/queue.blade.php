<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="5">
    <title>Màn hình hàng đợi - {{ $department }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <main class="flex min-h-screen flex-col p-6 sm:p-8 lg:p-10">
        <header class="flex flex-col gap-5 border-b border-white/10 pb-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.32em] text-cyan-300">Bệnh viện Smart Queue</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight sm:text-5xl lg:text-6xl">{{ $department }}</h1>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Thời gian hiện tại</p>
                <p id="current-time" class="mt-2 text-3xl font-black text-white">{{ now()->format('H:i:s') }}</p>
            </div>
        </header>

        <section class="grid flex-1 gap-6 py-6 lg:grid-cols-[1.15fr_0.85fr]">
            <div class="grid gap-6">
                <div class="rounded-[2rem] border border-cyan-400/20 bg-cyan-400/10 p-6 sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.28em] text-cyan-300">Đang gọi</p>
                    <div class="mt-5 flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                        <div>
                            <p class="text-7xl font-black leading-none text-white sm:text-8xl lg:text-9xl">
                                {{ $callingTicket?->displayNumber() ?? '---' }}
                            </p>
                            <p class="mt-5 text-2xl font-bold text-slate-200">
                                {{ $callingTicket?->patient_name ?? 'Đang chờ lượt gọi' }}
                            </p>
                        </div>
                        <div class="rounded-3xl bg-white/10 px-5 py-4">
                            <p class="text-sm font-black uppercase tracking-[0.2em] text-slate-300">Phòng khám</p>
                            <p class="mt-2 text-4xl font-black text-white">{{ $room }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.28em] text-slate-400">Đang khám</p>
                    <div class="mt-4 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-5xl font-black text-white sm:text-6xl">
                                {{ $servingTicket?->displayNumber() ?? '---' }}
                            </p>
                            <p class="mt-3 text-xl font-semibold text-slate-300">
                                {{ $servingTicket?->patient_name ?? 'Chưa có bệnh nhân đang khám' }}
                            </p>
                        </div>
                        @if($servingTicket)
                            <span class="rounded-full bg-emerald-400/15 px-4 py-2 text-sm font-black text-emerald-300">Đang phục vụ</span>
                        @endif
                    </div>
                </div>
            </div>

            <aside class="rounded-[2rem] border border-white/10 bg-white/5 p-6 sm:p-8">
                <p class="text-sm font-black uppercase tracking-[0.28em] text-slate-400">Tiếp theo</p>
                <div class="mt-6 grid gap-4">
                    @forelse($nextTickets as $ticket)
                        <div class="flex items-center justify-between rounded-3xl bg-white px-5 py-5 text-slate-950">
                            <span class="text-4xl font-black sm:text-5xl">{{ $ticket->displayNumber() }}</span>
                            <span class="text-right text-base font-bold text-slate-500">{{ $ticket->patient_name }}</span>
                        </div>
                    @empty
                        <div class="rounded-3xl border border-dashed border-white/15 px-5 py-10 text-center text-2xl font-bold text-slate-400">
                            Đang chờ lượt gọi
                        </div>
                    @endforelse
                </div>
            </aside>
        </section>

        <footer class="flex flex-col gap-2 border-t border-white/10 pt-5 text-sm font-semibold text-slate-400 sm:flex-row sm:items-center sm:justify-between">
            <span>Màn hình tự cập nhật mỗi 5 giây</span>
            <span>Vui lòng theo dõi số thứ tự và chuẩn bị giấy tờ khi gần đến lượt</span>
        </footer>
    </main>

    <script>
        const clock = document.getElementById('current-time');
        setInterval(() => {
            const now = new Date();
            clock.textContent = now.toLocaleTimeString('vi-VN', { hour12: false });
        }, 1000);
    </script>
</body>
</html>
