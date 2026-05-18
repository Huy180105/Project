<x-guest-layout>
    <div class="mb-6">
        <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-700">Đăng ký nhân sự</p>
        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Tạo tài khoản vận hành bệnh viện</h2>
        <p class="mt-2 text-sm font-medium leading-6 text-slate-600">
            Web dành cho bác sĩ, điều dưỡng và quầy tiếp nhận. Bệnh nhân sẽ sử dụng Android app để lấy số và theo dõi lượt khám.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <label class="block">
            <span class="text-sm font-black text-slate-700">Họ và tên</span>
            <input id="name" class="field mt-2" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Ví dụ: Nguyễn Văn A">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </label>

        <label class="block">
            <span class="text-sm font-black text-slate-700">Vai trò trên web</span>
            <select id="role" name="role" class="field mt-2" required>
                <option value="{{ \App\Models\User::ROLE_RECEPTIONIST }}" @selected(old('role') === \App\Models\User::ROLE_RECEPTIONIST)>Quầy tiếp nhận</option>
                <option value="{{ \App\Models\User::ROLE_NURSE }}" @selected(old('role') === \App\Models\User::ROLE_NURSE)>Điều dưỡng</option>
                <option value="{{ \App\Models\User::ROLE_DOCTOR }}" @selected(old('role') === \App\Models\User::ROLE_DOCTOR)>Bác sĩ</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </label>

        <label class="block">
            <span class="text-sm font-black text-slate-700">Email bệnh viện</span>
            <input id="email" class="field mt-2" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="doctor@hospital.local">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </label>

        <label class="block">
            <span class="text-sm font-black text-slate-700">Mật khẩu</span>
            <input id="password" class="field mt-2" type="password" name="password" required autocomplete="new-password" placeholder="Tối thiểu 8 ký tự">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </label>

        <label class="block">
            <span class="text-sm font-black text-slate-700">Nhập lại mật khẩu</span>
            <input id="password_confirmation" class="field mt-2" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại mật khẩu">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </label>

        <button class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-slate-800" type="submit">
            Tạo tài khoản web
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </button>

        <p class="text-center text-sm font-medium text-slate-600">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="font-black text-cyan-700 hover:text-cyan-900">Đăng nhập</a>
        </p>
    </form>
</x-guest-layout>
