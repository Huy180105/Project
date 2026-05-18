<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Smart Queue Hospital') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-950 antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,rgba(34,211,238,0.28),transparent_30%),radial-gradient(circle_at_top_right,rgba(99,102,241,0.18),transparent_32%),linear-gradient(180deg,#ffffff_0%,#effbff_52%,#fff8fb_100%)]">
            <div class="mx-auto grid min-h-screen max-w-6xl items-center gap-10 px-4 py-10 lg:grid-cols-[0.95fr_1.05fr]">
                <div class="hidden lg:block">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <div class="grid h-16 w-16 place-items-center rounded-3xl bg-white shadow-[0_14px_35px_rgba(14,165,233,0.18)]">
                            <x-brand-mark class="h-14 w-14" />
                        </div>
                        <div>
                            <p class="text-2xl font-black tracking-tight">Smart Queue</p>
                            <p class="text-sm font-semibold text-slate-500">Hospital Web Console</p>
                        </div>
                    </a>

                    <h1 class="mt-10 text-5xl font-black leading-tight tracking-tight text-slate-950">
                        Web console dành cho nhân sự bệnh viện.
                    </h1>
                    <p class="mt-5 max-w-xl text-lg font-medium leading-8 text-slate-600">
                        Bác sĩ, điều dưỡng và quầy tiếp nhận dùng web để gọi số, xác nhận HIS, xử lý vắng mặt và điều phối ưu tiên. Bệnh nhân sẽ dùng Android app.
                    </p>
                    <div class="mt-8 grid gap-4">
                        <div class="rounded-3xl bg-white/80 p-5 shadow-sm ring-1 ring-slate-100">
                            <p class="font-black text-slate-950">Vai trò nội bộ rõ ràng</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Bác sĩ, điều dưỡng và lễ tân có luồng thao tác riêng trong web console.</p>
                        </div>
                        <div class="rounded-3xl bg-cyan-50 p-5 ring-1 ring-cyan-100">
                            <p class="font-black text-cyan-900">Bệnh nhân ở kênh Android</p>
                            <p class="mt-2 text-sm leading-6 text-cyan-800">Android app sẽ xử lý lấy số, QR, theo dõi lượt và thông báo gần đến lượt.</p>
                        </div>
                    </div>
                </div>

                <div class="mx-auto w-full max-w-md rounded-[2rem] border border-white/80 bg-white/90 p-6 shadow-[0_30px_90px_rgba(15,23,42,0.12)]">
                    <div class="mb-6 flex items-center gap-3 lg:hidden">
                        <div class="grid h-14 w-14 place-items-center rounded-2xl bg-white shadow-sm">
                            <x-brand-mark class="h-12 w-12" />
                        </div>
                        <div>
                            <p class="text-xl font-black">Smart Queue</p>
                            <p class="text-xs font-semibold text-slate-500">Hospital Web Console</p>
                        </div>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
