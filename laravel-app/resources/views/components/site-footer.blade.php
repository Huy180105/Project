<footer class="bg-slate-950 py-14 text-slate-100">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[1.25fr_0.75fr_0.75fr] lg:px-8">
        <div>
            <div class="flex items-center gap-4">
                <div class="grid h-16 w-16 place-items-center rounded-3xl bg-white">
                    <x-brand-mark class="h-12 w-12" />
                </div>
                <div>
                    <h3 class="text-2xl font-black">Smart Queue Hospital</h3>
                    <p class="text-cyan-200">Hệ sinh thái hàng đợi thông minh cho bệnh viện</p>
                </div>
            </div>
            <p class="mt-6 max-w-2xl text-base leading-8 text-slate-300">
                Nền tảng hỗ trợ lấy số đa kênh, điều phối phòng khám, cảnh báo quá tải và kết nối HIS qua tầng middleware HL7/FHIR mô phỏng.
            </p>
            <div class="mt-6 space-y-3 text-slate-300">
                <p class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-cyan-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="m22 6-10 7L2 6"/></svg>
                    support@smartqueue-hospital.local
                </p>
                <p class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-cyan-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7"/></svg>
                    1900-QUEUE
                </p>
                <p class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-cyan-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    Khu điều phối khám bệnh, Bệnh viện mô phỏng X
                </p>
            </div>
        </div>

        <div>
            <h4 class="text-lg font-black">Liên kết nhanh</h4>
            <div class="mt-6 space-y-4 text-slate-300">
                <a href="{{ route('home') }}" class="flex items-center gap-3 hover:text-cyan-200">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
                    Trang chủ
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 hover:text-cyan-200">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('queue-tickets.index') }}" class="flex items-center gap-3 hover:text-cyan-200">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/><path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/></svg>
                        Hàng đợi
                    </a>
                    <a href="{{ route('documents.index') }}" class="flex items-center gap-3 hover:text-cyan-200">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                        Tài liệu RAG
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 hover:text-cyan-200">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 1 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                        Thông tin cá nhân
                    </a>
                @endauth
            </div>
        </div>

        <div>
            <h4 class="text-lg font-black">Năng lực hệ thống</h4>
            <div class="mt-6 space-y-4 text-slate-300">
                <p class="flex items-center gap-3"><svg class="h-5 w-5 text-cyan-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Lấy số Web, Kiosk, Quầy</p>
                <p class="flex items-center gap-3"><svg class="h-5 w-5 text-cyan-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8V4H8"/><rect x="4" y="8" width="16" height="12" rx="2"/></svg>AI Patient Assistant</p>
                <p class="flex items-center gap-3"><svg class="h-5 w-5 text-cyan-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>Dự đoán tải phòng khám</p>
                <p class="flex items-center gap-3"><svg class="h-5 w-5 text-cyan-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/></svg>Bảo vệ dữ liệu y tế</p>
            </div>
        </div>
    </div>
    <div class="mx-auto mt-12 max-w-7xl border-t border-white/10 px-4 pt-8 text-sm text-slate-400 sm:px-6 lg:px-8">
        © 2026 Smart Queue Hospital. Built for healthcare workflow, HIS integration and patient-friendly queue operations.
    </div>
</footer>
