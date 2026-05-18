<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-white/70 bg-white/85 shadow-[0_18px_60px_rgba(15,23,42,0.08)] backdrop-blur-2xl">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-20 items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-white shadow-[0_14px_35px_rgba(14,165,233,0.18)]">
                        <x-brand-mark class="h-11 w-11" />
                    </div>
                    <div>
                        <p class="text-lg font-black tracking-tight text-slate-950">Smart Queue</p>
                        <p class="text-xs font-medium text-slate-500">Hospital HIS Ecosystem</p>
                    </div>
                </a>

                <div class="hidden items-center rounded-2xl border border-slate-200/80 bg-white/70 p-1 shadow-sm lg:flex">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('home') ? 'bg-slate-950 text-white shadow-lg' : 'text-slate-600 hover:bg-cyan-50 hover:text-cyan-700' }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
                        Trang chủ
                    </a>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-slate-950 text-white shadow-lg' : 'text-slate-600 hover:bg-cyan-50 hover:text-cyan-700' }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('queue-tickets.index') }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('queue-tickets.*') ? 'bg-slate-950 text-white shadow-lg' : 'text-slate-600 hover:bg-cyan-50 hover:text-cyan-700' }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/><path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/></svg>
                        Hàng đợi
                    </a>
                    <a href="{{ route('doctor.queue.index') }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('doctor.queue.*') ? 'bg-slate-950 text-white shadow-lg' : 'text-slate-600 hover:bg-cyan-50 hover:text-cyan-700' }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9"/></svg>
                        Bảng gọi khám
                    </a>
                    <a href="{{ route('reception.queue.index') }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('reception.queue.*') ? 'bg-slate-950 text-white shadow-lg' : 'text-slate-600 hover:bg-cyan-50 hover:text-cyan-700' }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                        Quầy tiếp nhận
                    </a>
                    <a href="{{ route('ai-chat.index') }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('ai-chat.*') ? 'bg-slate-950 text-white shadow-lg' : 'text-slate-600 hover:bg-cyan-50 hover:text-cyan-700' }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8V4H8"/><rect x="4" y="8" width="16" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/></svg>
                        AI Tư vấn
                    </a>
                    <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('documents.*') ? 'bg-slate-950 text-white shadow-lg' : 'text-slate-600 hover:bg-cyan-50 hover:text-cyan-700' }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                        Tài liệu
                    </a>
                </div>
            </div>

            <div class="hidden items-center gap-3 sm:flex">
                <div class="hidden items-center gap-2 rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-2 lg:flex">
                    <span class="h-2.5 w-2.5 rounded-full bg-cyan-500 shadow-[0_0_0_6px_rgba(6,182,212,0.14)]"></span>
                    <span class="text-sm font-semibold text-cyan-800">Queue Engine Online</span>
                </div>

                <details class="relative">
                    <summary class="flex cursor-pointer list-none items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm transition hover:border-cyan-300 hover:shadow-lg">
                        <div class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-cyan-500 via-blue-500 to-violet-500 text-sm font-black text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden text-left lg:block">
                            <p class="text-sm font-bold text-slate-950">{{ Auth::user()->name }}</p>
                            <p class="text-xs font-medium text-slate-500">{{ ucfirst(Auth::user()->role ?? 'user') }}</p>
                        </div>
                    </summary>

                    <div class="absolute right-0 z-50 mt-4 w-60 rounded-2xl border border-slate-200 bg-white p-3 shadow-2xl">
                        <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-cyan-50 hover:text-cyan-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
                            Quay lại trang chủ
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-violet-50 hover:text-violet-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 1 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                            Thông tin cá nhân
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="mt-1 flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-sm font-semibold text-rose-600 transition hover:bg-rose-50">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </details>
            </div>

            <button @click="open = ! open" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm sm:hidden">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path x-show="!open" d="M4 7h16M4 12h16M4 17h16" />
                    <path x-show="open" d="M6 6l12 12M18 6 6 18" />
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-transition class="border-t border-slate-200 bg-white/95 px-4 py-4 sm:hidden">
        <div class="space-y-2">
            <a href="{{ route('home') }}" class="block rounded-2xl px-4 py-3 text-sm font-bold {{ request()->routeIs('home') ? 'bg-slate-950 text-white' : 'text-slate-700 hover:bg-cyan-50' }}">Trang chủ</a>
            <a href="{{ route('dashboard') }}" class="block rounded-2xl px-4 py-3 text-sm font-bold {{ request()->routeIs('dashboard') ? 'bg-slate-950 text-white' : 'text-slate-700 hover:bg-cyan-50' }}">Dashboard</a>
            <a href="{{ route('queue-tickets.index') }}" class="block rounded-2xl px-4 py-3 text-sm font-bold {{ request()->routeIs('queue-tickets.*') ? 'bg-slate-950 text-white' : 'text-slate-700 hover:bg-cyan-50' }}">Hàng đợi</a>
            <a href="{{ route('doctor.queue.index') }}" class="block rounded-2xl px-4 py-3 text-sm font-bold {{ request()->routeIs('doctor.queue.*') ? 'bg-slate-950 text-white' : 'text-slate-700 hover:bg-cyan-50' }}">Bảng gọi khám</a>
            <a href="{{ route('reception.queue.index') }}" class="block rounded-2xl px-4 py-3 text-sm font-bold {{ request()->routeIs('reception.queue.*') ? 'bg-slate-950 text-white' : 'text-slate-700 hover:bg-cyan-50' }}">Quầy tiếp nhận</a>
            <a href="{{ route('ai-chat.index') }}" class="block rounded-2xl px-4 py-3 text-sm font-bold {{ request()->routeIs('ai-chat.*') ? 'bg-slate-950 text-white' : 'text-slate-700 hover:bg-cyan-50' }}">AI Tư vấn</a>
            <a href="{{ route('documents.index') }}" class="block rounded-2xl px-4 py-3 text-sm font-bold {{ request()->routeIs('documents.*') ? 'bg-slate-950 text-white' : 'text-slate-700 hover:bg-cyan-50' }}">Tài liệu</a>
        </div>
    </div>
</nav>
