<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Smart Queue Hospital') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 font-sans text-slate-950 antialiased">
        <div class="min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,rgba(34,211,238,0.28),transparent_30%),radial-gradient(circle_at_top_right,rgba(99,102,241,0.18),transparent_32%),radial-gradient(circle_at_bottom_left,rgba(251,113,133,0.16),transparent_28%),linear-gradient(180deg,#ffffff_0%,#effbff_45%,#fff8fb_100%)]">
            <nav class="sticky top-0 z-40 border-b border-white/70 bg-white/85 shadow-[0_18px_60px_rgba(15,23,42,0.08)] backdrop-blur-2xl">
                <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <div class="grid h-14 w-14 place-items-center rounded-2xl bg-white shadow-[0_14px_35px_rgba(14,165,233,0.18)]">
                            <x-brand-mark class="h-12 w-12" />
                        </div>
                        <div>
                            <p class="text-xl font-black tracking-tight">Smart Queue</p>
                            <p class="text-xs font-semibold text-slate-500">Hospital Web Console</p>
                        </div>
                    </a>

                    <div class="hidden items-center gap-7 text-sm font-bold text-slate-600 lg:flex">
                        <a href="#staff-web" class="transition hover:text-cyan-700">Web bệnh viện</a>
                        <a href="#patient-android" class="transition hover:text-cyan-700">Android bệnh nhân</a>
                        <a href="#integration" class="transition hover:text-cyan-700">HIS</a>
                        <a href="#roadmap" class="transition hover:text-cyan-700">Roadmap</a>
                    </div>

                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white shadow-lg transition hover:-translate-y-0.5">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 shadow-sm transition hover:border-cyan-300 hover:text-cyan-700">Đăng nhập</a>
                            <a href="{{ route('register') }}" class="hidden rounded-2xl bg-slate-950 px-4 py-2.5 text-sm font-black text-white shadow-lg sm:inline-flex">Tài khoản nhân sự</a>
                        @endauth
                    </div>
                </div>
            </nav>

            <main>
                <section id="overview" class="relative mx-auto grid min-h-[760px] max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-[1.03fr_0.97fr] lg:px-8">
                    <div class="absolute left-10 top-28 h-40 w-40 rotate-12 rounded-[2rem] bg-cyan-100/80 blur-sm"></div>
                    <div class="absolute right-16 top-40 h-52 w-52 -rotate-12 rounded-[2.5rem] bg-violet-100/80 blur-sm"></div>
                    <div class="absolute bottom-20 left-1/3 h-44 w-64 rotate-6 rounded-[2rem] bg-rose-100/70 blur-sm"></div>

                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 rounded-full border border-cyan-200 bg-cyan-50 px-4 py-2 text-sm font-black text-cyan-700 shadow-sm">
                            <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                            Web cho bệnh viện, Android cho bệnh nhân
                        </div>
                        <h1 class="mt-7 max-w-4xl text-5xl font-black leading-[0.98] tracking-tight text-slate-950 sm:text-6xl lg:text-7xl">
                            Cổng vận hành hàng đợi thông minh cho bệnh viện.
                        </h1>
                        <p class="mt-6 max-w-2xl text-lg font-medium leading-8 text-slate-600">
                            Web tập trung cho bác sĩ, điều dưỡng, lễ tân và quản trị bệnh viện: gọi số, xác nhận HIS, xử lý vắng mặt, ưu tiên cấp cứu và theo dõi tải phòng khám. Bệnh nhân sẽ dùng Android app để lấy số, theo dõi lượt và nhận thông báo.
                        </p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-500 via-blue-500 to-violet-500 px-6 py-4 text-base font-black text-white shadow-[0_18px_45px_rgba(14,165,233,0.28)] transition hover:-translate-y-0.5">
                                    Mở Dashboard
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-500 via-blue-500 to-violet-500 px-6 py-4 text-base font-black text-white shadow-[0_18px_45px_rgba(14,165,233,0.28)] transition hover:-translate-y-0.5">
                                    Tạo tài khoản nhân sự
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                                </a>
                            @endauth
                            <a href="#patient-android" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white/85 px-6 py-4 text-base font-black text-slate-800 shadow-sm transition hover:border-cyan-300 hover:text-cyan-700">
                                Xem vai trò Android
                            </a>
                        </div>
                    </div>

                    <div class="relative z-10">
                        <div class="rounded-[2rem] border border-white/80 bg-white/80 p-5 shadow-[0_30px_90px_rgba(15,23,42,0.12)]">
                            <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-5">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[0.24em] text-cyan-600">Hospital Console</p>
                                    <h2 class="mt-2 text-2xl font-black text-slate-950">Điều phối phòng khám</h2>
                                </div>
                                <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-black text-cyan-700">Staff Web</span>
                            </div>
                            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                <div class="rounded-[1.5rem] bg-gradient-to-br from-cyan-500 to-blue-500 p-5 text-white shadow-[0_18px_45px_rgba(14,165,233,0.20)]">
                                    <p class="text-sm font-black">Đang chờ</p>
                                    <p class="mt-3 text-4xl font-black">126</p>
                                    <p class="mt-2 text-sm font-semibold text-cyan-50">lọc theo khoa và ưu tiên</p>
                                </div>
                                <div class="rounded-[1.5rem] bg-white p-5 shadow-sm ring-1 ring-slate-100">
                                    <p class="text-sm font-black text-slate-600">Số đang gọi</p>
                                    <p class="mt-3 text-4xl font-black text-slate-950">A042</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-500">Phòng Nội 203</p>
                                </div>
                                <div class="rounded-[1.5rem] bg-white p-5 shadow-sm ring-1 ring-slate-100">
                                    <p class="text-sm font-black text-slate-600">Chờ HIS</p>
                                    <p class="mt-3 text-4xl font-black text-violet-700">18</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-500">cần xác nhận thanh toán</p>
                                </div>
                                <div class="rounded-[1.5rem] bg-rose-50 p-5 ring-1 ring-rose-100">
                                    <p class="text-sm font-black text-rose-700">Cấp cứu</p>
                                    <p class="mt-3 text-4xl font-black text-rose-700">3</p>
                                    <p class="mt-2 text-sm font-semibold text-rose-600">được chèn ưu tiên</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="staff-web" class="bg-white/75 py-20">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="max-w-3xl">
                            <span class="rounded-full border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-black text-blue-700">Web Console</span>
                            <h2 class="mt-5 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Web chỉ phục vụ vận hành nội bộ bệnh viện.</h2>
                            <p class="mt-5 text-lg font-medium leading-8 text-slate-600">Màn hình web ưu tiên tốc độ thao tác, ít bước, nút rõ ràng và dữ liệu đủ cho ca trực đông bệnh nhân.</p>
                        </div>

                        <div class="mt-12 grid gap-6 lg:grid-cols-3">
                            @foreach ([
                                ['title' => 'Lễ tân', 'desc' => 'Tạo số, xác nhận BHYT/thanh toán, chọn khoa, in phiếu hoặc hỗ trợ kiosk.'],
                                ['title' => 'Điều dưỡng', 'desc' => 'Gọi số tiếp theo, đánh dấu vắng mặt, gọi lại, chèn cấp cứu và điều phối phòng chờ.'],
                                ['title' => 'Bác sĩ', 'desc' => 'Xem số đang gọi, danh sách tiếp theo, tải phòng khám và hoàn tất lượt khám.'],
                            ] as $item)
                                <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-[0_24px_80px_rgba(15,23,42,0.08)]">
                                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-cyan-50 text-cyan-700">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                                    </div>
                                    <h3 class="mt-6 text-xl font-black text-slate-950">{{ $item['title'] }}</h3>
                                    <p class="mt-3 text-sm font-medium leading-7 text-slate-600">{{ $item['desc'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section id="patient-android" class="py-20">
                    <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                        <div class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-[0_30px_90px_rgba(15,23,42,0.20)]">
                            <p class="text-sm font-black uppercase tracking-[0.24em] text-cyan-300">Android Patient App</p>
                            <h2 class="mt-5 text-4xl font-black tracking-tight">Bệnh nhân không thao tác trên web vận hành.</h2>
                            <p class="mt-5 text-base font-medium leading-8 text-slate-300">Android app sẽ là kênh dành cho bệnh nhân: đăng nhập, lấy số, xem QR, theo dõi lượt, nhận thông báo và hỏi AI Patient Assistant.</p>
                        </div>
                        <div class="grid gap-4">
                            @foreach ([
                                ['label' => 'Patient API', 'value' => 'Laravel/Lumen cung cấp REST API cho app Android.'],
                                ['label' => 'Queue Tracking', 'value' => 'App hiển thị số hiện tại, vị trí trong hàng và thời gian chờ dự kiến.'],
                                ['label' => 'Notification', 'value' => 'Firebase hoặc n8n gửi thông báo khi còn 5 lượt.'],
                                ['label' => 'QR Ticket', 'value' => 'Bệnh nhân dùng QR để check-in hoặc đối chiếu tại kiosk/quầy.'],
                            ] as $row)
                                <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                                    <p class="text-sm font-black text-cyan-700">{{ $row['label'] }}</p>
                                    <p class="mt-2 text-lg font-bold text-slate-950">{{ $row['value'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section id="integration" class="bg-white/75 py-20">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="rounded-[2rem] border border-slate-100 bg-white p-8 shadow-[0_24px_80px_rgba(15,23,42,0.08)] lg:p-10">
                            <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr]">
                                <div>
                                    <span class="rounded-full bg-violet-50 px-4 py-2 text-sm font-black text-violet-700">HIS + Middleware</span>
                                    <h2 class="mt-5 text-4xl font-black tracking-tight text-slate-950">Web vận hành nói chuyện với HIS, Android chỉ nhìn phần cần cho bệnh nhân.</h2>
                                    <p class="mt-5 text-base font-medium leading-8 text-slate-600">Khi quầy xác nhận thanh toán, web cập nhật số từ pending sang ready. Android chỉ nhận trạng thái đã lọc: số của bạn, phòng khám và thông báo gần đến lượt.</p>
                                </div>
                                <div class="rounded-[1.7rem] bg-slate-50 p-5">
                                    <div class="rounded-[1.4rem] bg-white p-5 text-sm font-semibold leading-7 text-slate-700 shadow-sm">Receptionist confirms BHYT/payment in Web Console.</div>
                                    <div class="ml-auto mt-4 max-w-xl rounded-[1.4rem] bg-gradient-to-br from-cyan-500 to-violet-500 p-5 text-sm font-bold leading-7 text-white shadow-sm">Queue Engine marks ticket as Ready, nurse can call, Android receives queue status.</div>
                                    <div class="mt-4 rounded-2xl bg-cyan-50 px-4 py-3 text-sm font-black text-cyan-800">HL7/FHIR simulation + REST API + notification workflow</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="roadmap" class="py-20">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="max-w-3xl">
                            <span class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-black text-rose-700">Next Build Order</span>
                            <h2 class="mt-5 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Thứ tự tiếp theo không bị loạn feature.</h2>
                        </div>
                        <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                            @foreach (['Doctor Panel', 'Receptionist Panel', 'Kiosk Mode', 'Public TV Display', 'Android Patient App', 'OpenAI thật', 'n8n Notification', 'Cisco/GNS3'] as $step)
                                <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                                    <p class="text-sm font-black text-slate-500">Phase {{ $loop->iteration }}</p>
                                    <p class="mt-3 text-lg font-black text-slate-950">{{ $step }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            </main>

            <x-site-footer />
        </div>
    </body>
</html>
