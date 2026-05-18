<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Gợi ý thuốc - {{ config('app.name', 'Health AI Platform') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 font-sans text-slate-950 antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,rgba(34,211,238,0.28),transparent_30%),radial-gradient(circle_at_top_right,rgba(168,85,247,0.22),transparent_32%),radial-gradient(circle_at_bottom_left,rgba(251,113,133,0.16),transparent_28%),linear-gradient(180deg,#ffffff_0%,#f8fbff_100%)]">
            <nav class="border-b border-white/70 bg-white/85 shadow-[0_18px_60px_rgba(15,23,42,0.08)] backdrop-blur-2xl">
                <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <a href="{{ url('/') }}" class="flex items-center gap-3">
                        <div class="grid h-14 w-14 place-items-center rounded-2xl bg-white shadow-[0_14px_35px_rgba(14,165,233,0.18)]">
                            <x-brand-mark class="h-12 w-12" />
                        </div>
                        <div>
                            <p class="text-xl font-black tracking-tight"><span class="text-cyan-600">Health</span> AI</p>
                            <p class="text-xs font-semibold text-slate-500">Gợi ý thuốc theo tình trạng</p>
                        </div>
                    </a>

                    <div class="flex items-center gap-3">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 shadow-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                            Trang chủ
                        </a>
                        @auth
                            <details class="relative">
                                <summary class="flex cursor-pointer list-none items-center gap-3 rounded-2xl bg-slate-950 px-4 py-2.5 text-sm font-black text-white shadow-lg">
                                    {{ Auth::user()->name }}
                                </summary>
                                <div class="absolute right-0 z-50 mt-3 w-56 rounded-2xl border border-slate-200 bg-white p-3 shadow-2xl">
                                    <a href="{{ route('dashboard') }}" class="block rounded-xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-cyan-50">Dashboard</a>
                                    <a href="{{ route('profile.edit') }}" class="block rounded-xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-violet-50">Sửa thông tin cá nhân</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="mt-1 w-full rounded-xl px-4 py-3 text-left text-sm font-bold text-rose-600 hover:bg-rose-50">Đăng xuất</button>
                                    </form>
                                </div>
                            </details>
                        @else
                            <a href="{{ route('login') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 shadow-sm">Đăng nhập</a>
                            <a href="{{ route('register') }}" class="hidden rounded-2xl bg-slate-950 px-4 py-2.5 text-sm font-black text-white shadow-lg sm:inline-flex">Đăng ký</a>
                        @endauth
                    </div>
                </div>
            </nav>

            <main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <section class="grid gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-start">
                    <div>
                        <a href="{{ url('/') }}" class="mb-6 inline-flex items-center gap-2 rounded-2xl border border-cyan-200 bg-white/80 px-4 py-2 text-sm font-black text-cyan-700 shadow-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                            Quay lại trang chủ
                        </a>
                        <span class="rounded-full border border-cyan-200 bg-cyan-50 px-4 py-2 text-sm font-black text-cyan-700">Gợi ý thuốc</span>
                        <h1 class="mt-6 text-5xl font-black leading-tight tracking-tight sm:text-6xl">Gợi ý có cần dùng thuốc không dựa trên tình trạng hiện tại.</h1>
                        <p class="mt-6 max-w-2xl text-lg font-medium leading-8 text-slate-600">
                            Nhập triệu chứng hoặc đăng nhập để hệ thống xem health log mới nhất. Kết quả chỉ là gợi ý tham khảo, không thay thế chẩn đoán của bác sĩ hoặc tư vấn trực tiếp của dược sĩ.
                        </p>

                        <div class="mt-8 rounded-3xl border border-amber-200 bg-amber-50 p-5 text-amber-900">
                            <p class="font-black">Lưu ý an toàn</p>
                            <p class="mt-2 text-sm font-medium leading-6">Hệ thống không kết luận bạn mắc bệnh gì. Nếu có đau ngực, khó thở, sốt cao kéo dài, mất ý thức, dị ứng nặng hoặc triệu chứng bất thường, hãy liên hệ cơ sở y tế ngay.</p>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-[0_30px_90px_rgba(15,23,42,0.12)] backdrop-blur-2xl">
                        <h2 class="text-2xl font-black">Hỏi AI về triệu chứng</h2>
                        <p class="mt-2 text-sm font-medium text-slate-600">Mô tả triệu chứng, thời gian xuất hiện, mức độ và bệnh nền nếu có.</p>

                        <form class="mt-5 space-y-4" method="GET" action="{{ route('store') }}">
                            <textarea name="symptoms" rows="5" class="field" placeholder="Ví dụ: Tôi ho khan 3 ngày, hơi đau họng, không sốt, ngủ ít và mệt...">{{ $symptoms }}</textarea>
                            <button class="btn-primary w-full" type="submit">Phân tích triệu chứng</button>
                        </form>

                        @if ($symptoms)
                            @php
                                $text = mb_strtolower($symptoms);
                                $hasFever = str_contains($text, 'sốt') || str_contains($text, 'nong') || str_contains($text, 'nóng');
                                $hasCough = str_contains($text, 'ho') || str_contains($text, 'đau họng') || str_contains($text, 'dau hong');
                                $hasTired = str_contains($text, 'mệt') || str_contains($text, 'met') || str_contains($text, 'uể oải');
                                $hasSleep = str_contains($text, 'mất ngủ') || str_contains($text, 'mat ngu') || str_contains($text, 'ngủ ít');
                            @endphp
                            <div class="mt-6 rounded-3xl bg-gradient-to-br from-cyan-50 via-white to-violet-50 p-5">
                                <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-700">Kết quả tham khảo</p>
                                <h3 class="mt-3 text-2xl font-black">AI không chẩn đoán bệnh, nhưng có thể gợi ý hướng xử lý ban đầu.</h3>
                                <div class="mt-4 space-y-3 text-sm font-medium leading-6 text-slate-700">
                                    @if ($hasFever)
                                        <p>Sốt có thể liên quan đến nhiễm trùng, viêm hoặc phản ứng cơ thể. Bạn nên theo dõi nhiệt độ, uống đủ nước và đi khám nếu sốt cao hoặc kéo dài.</p>
                                    @endif
                                    @if ($hasCough)
                                        <p>Ho hoặc đau họng có thể do kích ứng, cảm lạnh, viêm họng hoặc yếu tố môi trường. Có thể cân nhắc sản phẩm làm dịu họng, nước muối sinh lý hoặc hỏi dược sĩ nếu cần thuốc.</p>
                                    @endif
                                    @if ($hasTired || $hasSleep)
                                        <p>Mệt mỏi hoặc ngủ ít nên xem lại giấc ngủ, lượng nước, stress và dinh dưỡng trước khi dùng vitamin hoặc sản phẩm bổ sung.</p>
                                    @endif
                                    @if (! $hasFever && ! $hasCough && ! $hasTired && ! $hasSleep)
                                        <p>Triệu chứng của bạn cần thêm thông tin về thời gian, mức độ, tuổi, thuốc đang dùng và bệnh nền để gợi ý an toàn hơn.</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="mt-10 grid gap-6 lg:grid-cols-3">
                    <div class="rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-[0_24px_70px_rgba(15,23,42,0.09)]">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-600">Dữ liệu hiện tại</p>
                        @auth
                            @if ($latestHealthLog)
                                <div class="mt-5 space-y-3 text-sm font-bold text-slate-700">
                                    <p>Nhịp tim: {{ $latestHealthLog->heart_rate ?? 'Chưa có' }} bpm</p>
                                    <p>Giấc ngủ: {{ $latestHealthLog->sleep_hours ?? 'Chưa có' }} giờ</p>
                                    <p>Lượng nước: {{ $latestHealthLog->water_intake ?? 'Chưa có' }}</p>
                                    <p>Tâm trạng: {{ $latestHealthLog->mood ?? 'Chưa có' }}</p>
                                    <p>Triệu chứng đã ghi: {{ $latestHealthLog->symptoms ?? 'Chưa có' }}</p>
                                </div>
                            @else
                                <p class="mt-5 text-sm font-medium leading-6 text-slate-600">Bạn chưa có health log. Hãy thêm health log để gợi ý thuốc sát tình trạng hơn.</p>
                                <a href="{{ route('health-logs.create') }}" class="btn-primary mt-5">Thêm health log</a>
                            @endif
                        @else
                            <p class="mt-5 text-sm font-medium leading-6 text-slate-600">Đăng nhập để hệ thống đọc health log mới nhất và cá nhân hóa gợi ý.</p>
                            <a href="{{ route('login') }}" class="btn-primary mt-5">Đăng nhập</a>
                        @endauth
                    </div>

                    @foreach ([
                        ['Theo dõi trước khi dùng thuốc', 'Nếu triệu chứng nhẹ, hãy theo dõi nhiệt độ, giấc ngủ, nước uống và mức độ mệt trong 24-48h.'],
                        ['Khi nên hỏi dược sĩ', 'Khi bạn đang dùng thuốc khác, có bệnh nền, dị ứng thuốc, phụ nữ mang thai hoặc triệu chứng kéo dài.'],
                        ['Khi nên đi khám', 'Sốt cao, đau ngực, khó thở, mất nước, lơ mơ, đau dữ dội hoặc triệu chứng nặng dần cần được đánh giá y tế.'],
                    ] as [$title, $desc])
                        <div class="rounded-[2rem] border border-white/80 bg-white/80 p-6 shadow-[0_24px_70px_rgba(15,23,42,0.09)]">
                            <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-cyan-400 via-violet-500 to-rose-400"></div>
                            <h3 class="mt-5 text-xl font-black">{{ $title }}</h3>
                            <p class="mt-3 text-sm font-medium leading-6 text-slate-600">{{ $desc }}</p>
                        </div>
                    @endforeach
                </section>

                <section class="mt-10 rounded-[2rem] bg-slate-950 p-6 text-white shadow-[0_30px_90px_rgba(15,23,42,0.20)] sm:p-8">
                    <div class="grid gap-6 lg:grid-cols-[1fr_1.2fr] lg:items-center">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-300">Danh mục tham khảo</p>
                            <h2 class="mt-3 text-3xl font-black">Sản phẩm chỉ nên chọn sau khi hiểu tình trạng.</h2>
                            <p class="mt-4 text-slate-300">Các nhóm dưới đây là danh mục tham khảo để hỏi dược sĩ, không phải đơn thuốc tự động.</p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ([
                                ['Làm dịu họng', 'Khi ho nhẹ, khô họng, kích ứng.'],
                                ['Bù nước & điện giải', 'Khi mất nước nhẹ, vận động nhiều, mệt.'],
                                ['Vitamin hỗ trợ', 'Khi ăn uống thiếu chất hoặc phục hồi.'],
                                ['Thiết bị theo dõi', 'Nhiệt kế, máy đo huyết áp, SpO2.'],
                            ] as [$name, $desc])
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="font-black text-white">{{ $name }}</p>
                                    <p class="mt-2 text-sm text-slate-300">{{ $desc }}</p>
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
