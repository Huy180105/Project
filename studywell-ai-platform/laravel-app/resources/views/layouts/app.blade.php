<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen">
        <aside class="fixed inset-y-0 left-0 hidden w-64 border-r border-slate-200 bg-white px-5 py-6 lg:block">
            <a href="{{ route('dashboard') }}" class="text-lg font-bold text-slate-950">StudyWell AI</a>
            <nav class="mt-8 space-y-1">
                <a class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100" href="{{ route('wellness-signals.index') }}">Study Signals</a>
                <a class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100" href="{{ route('ai-chat.index') }}">AI Chat</a>
                <a class="block rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100" href="{{ route('documents.index') }}">Documents</a>
            </nav>
        </aside>

        <main class="lg:pl-64">
            <header class="border-b border-slate-200 bg-white px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Student Wellness RAG Platform</p>
                        <h1 class="text-xl font-bold text-slate-950">@yield('title', 'Dashboard')</h1>
                    </div>
                    <div class="flex gap-2 text-sm lg:hidden">
                        <a href="{{ route('dashboard') }}" class="btn-secondary">Dashboard</a>
                        <a href="{{ route('ai-chat.index') }}" class="btn-secondary">AI</a>
                    </div>
                </div>
            </header>

            <section class="px-4 py-6 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                        {{ $errors->first() }}
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>
</body>
</html>
