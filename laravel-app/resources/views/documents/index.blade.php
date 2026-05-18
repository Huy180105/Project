<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-cyan-200 bg-cyan-50 px-4 py-2 text-xs font-black uppercase tracking-[0.24em] text-cyan-700">
                    <span class="h-2.5 w-2.5 rounded-full bg-cyan-500"></span>
                    RAG Knowledge Base
                </div>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Tài liệu quy trình bệnh viện</h1>
                <p class="mt-3 max-w-2xl text-base font-medium leading-7 text-slate-600">
                    Tải PDF, DOCX hoặc TXT để mô phỏng pipeline: parse, chunk, embedding, Pinecone và MongoDB logs cho AI Patient Assistant.
                </p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 shadow-sm transition hover:border-cyan-300 hover:text-cyan-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
                Trang chủ
            </a>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[1fr_0.8fr]">
        <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            @if(session('status'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <h2 class="text-2xl font-black text-slate-950">Upload tài liệu cho RAG</h2>
            <p class="mt-2 text-sm font-medium leading-7 text-slate-600">Ví dụ: quy trình khám BHYT, nội quy phòng chờ, hướng dẫn phân luồng cấp cứu, FAQ cho bệnh nhân.</p>

            <form class="mt-6 space-y-5" method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
                @csrf
                <label class="block">
                    <span class="text-sm font-black text-slate-700">Tên tài liệu</span>
                    <input class="field mt-2" type="text" name="title" placeholder="Ví dụ: Quy trình khám BHYT">
                </label>
                <label class="block">
                    <span class="text-sm font-black text-slate-700">File tài liệu</span>
                    <input class="field mt-2" type="file" name="document" accept=".pdf,.docx,.txt" required>
                    @error('document')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </label>
                <button class="inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:bg-slate-800" type="submit">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5"/><path d="M12 3v12"/></svg>
                    Upload và ingest
                </button>
            </form>
        </section>

        <aside class="space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black text-slate-950">Pipeline mô phỏng</h2>
                <div class="mt-5 space-y-3">
                    @foreach (['Upload', 'Parse text', 'Chunking', 'Embedding', 'Pinecone search', 'MongoDB log'] as $step)
                        <div class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700">
                            <span class="grid h-7 w-7 place-items-center rounded-full bg-cyan-100 text-xs font-black text-cyan-700">{{ $loop->iteration }}</span>
                            {{ $step }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-3xl bg-violet-50 p-6 ring-1 ring-violet-100">
                <p class="text-sm font-black uppercase tracking-[0.2em] text-violet-700">AI context</p>
                <p class="mt-4 text-sm font-medium leading-7 text-violet-900">Khi AI trả lời về quy trình khám, hệ thống có thể truy xuất tài liệu đã upload để trả lời có nguồn tham chiếu.</p>
            </div>
        </aside>
    </div>
</x-app-layout>
