<x-guest-layout>
    <div class="mb-6">
        <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-700">Đăng nhập</p>
        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Chào mừng quay lại</h2>
        <p class="mt-2 text-sm font-medium leading-6 text-slate-600">Truy cập dashboard hàng đợi, AI tư vấn và thông tin cá nhân.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <label class="block">
            <span class="text-sm font-black text-slate-700">Email</span>
            <input id="email" class="field mt-2" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </label>

        <label class="block">
            <span class="text-sm font-black text-slate-700">Mật khẩu</span>
            <input id="password" class="field mt-2" type="password" name="password" required autocomplete="current-password" placeholder="Nhập mật khẩu">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </label>

        <div class="flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-cyan-600 shadow-sm focus:ring-cyan-500" name="remember">
                <span class="ms-2 text-sm font-medium text-slate-600">Ghi nhớ đăng nhập</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-bold text-cyan-700 hover:text-cyan-900" href="{{ route('password.request') }}">
                    Quên mật khẩu?
                </a>
            @endif
        </div>

        <button class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-slate-800" type="submit">
            Đăng nhập
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </button>

        <p class="text-center text-sm font-medium text-slate-600">
            Chưa có tài khoản?
            <a href="{{ route('register') }}" class="font-black text-cyan-700 hover:text-cyan-900">Đăng ký ngay</a>
        </p>
    </form>
</x-guest-layout>
