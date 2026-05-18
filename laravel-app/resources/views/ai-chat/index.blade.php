<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-4 py-2 text-xs font-black uppercase tracking-[0.24em] text-violet-700">
                    <span class="h-2 w-2 rounded-full bg-violet-500"></span>
                    AI Patient Assistant
                </div>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Trợ lý AI cho bệnh nhân và điều phối</h1>
                <p class="mt-3 max-w-2xl text-base font-medium leading-7 text-slate-600">
                    Hỏi về triệu chứng, khoa khám phù hợp, giấy tờ cần chuẩn bị và trạng thái hàng đợi. AI chỉ hỗ trợ tham khảo, không thay thế bác sĩ.
                </p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-secondary inline-flex gap-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                Quay lại Dashboard
            </a>
        </div>
    </x-slot>

    @php
        $messages = $history ?? [];
        $prompts = [
            'Tôi đau ngực và khó thở, tôi nên làm gì?',
            'Tôi cần chuẩn bị giấy tờ gì khi đi khám BHYT?',
            'Còn bao lâu đến lượt khám nếu trước tôi còn 5 người?',
            'Triệu chứng sốt, ho và đau họng nên khám khoa nào?',
        ];
    @endphp

    <div class="grid min-h-[760px] gap-6 xl:grid-cols-[320px_minmax(0,1fr)]">
        <aside class="rounded-[2rem] border border-white/80 bg-white/80 p-5 shadow-[0_24px_80px_rgba(15,23,42,0.10)]">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-950">Lịch sử tư vấn</h2>
                <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-black text-violet-700">{{ count($messages) }}</span>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($messages as $item)
                    <a href="#latest-answer" class="block rounded-2xl border border-slate-100 bg-white p-4 shadow-sm transition hover:border-violet-200 hover:bg-violet-50/60">
                        <p class="line-clamp-2 text-sm font-bold text-slate-800">{{ $item['question'] }}</p>
                        <p class="mt-2 text-xs font-semibold text-slate-400">{{ $item['created_at'] ?? 'now' }}</p>
                    </a>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white/70 p-4 text-sm font-medium leading-6 text-slate-500">
                        Chưa có hội thoại nào. Hãy bắt đầu bằng một câu hỏi về triệu chứng, quy trình khám hoặc hàng đợi.
                    </div>
                @endforelse
            </div>

            <div class="mt-6 rounded-2xl bg-cyan-50 p-4 text-sm font-medium leading-6 text-cyan-900">
                Dấu hiệu như đau ngực, khó thở, yếu liệt, chảy máu nhiều hoặc mất ý thức cần báo điều dưỡng ngay.
            </div>
        </aside>

        <section class="flex min-h-[760px] flex-col overflow-hidden rounded-[2rem] border border-white/80 bg-white/80 shadow-[0_24px_90px_rgba(15,23,42,0.12)]">
            <div class="border-b border-white/80 bg-white/80 px-5 py-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-white shadow-sm">
                            <x-brand-mark class="h-10 w-10" />
                        </div>
                        <div>
                            <h2 class="font-black text-slate-950">AI Patient Assistant</h2>
                            <p class="text-sm font-medium text-slate-500">Triệu chứng, quy trình khám và hàng đợi</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2 text-sm font-black text-cyan-700">
                            <span class="h-2 w-2 animate-pulse rounded-full bg-cyan-500"></span>
                            Online
                        </div>
                        @if(count($messages))
                            <form method="POST" action="{{ route('ai-chat.history.destroy') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-700 shadow-sm transition hover:border-rose-300 hover:text-rose-700">
                                    Xóa lịch sử
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex-1 space-y-6 overflow-y-auto bg-[radial-gradient(circle_at_top_left,rgba(14,165,233,0.12),transparent_28%),radial-gradient(circle_at_top_right,rgba(124,58,237,0.10),transparent_30%)] p-5 lg:p-8">
                @if (! count($messages))
                    <div class="mx-auto max-w-3xl py-10 text-center">
                        <div class="mx-auto grid h-20 w-20 place-items-center rounded-[1.7rem] bg-white shadow-[0_20px_60px_rgba(15,23,42,0.12)]">
                            <x-brand-mark class="h-16 w-16" />
                        </div>
                        <h3 class="mt-6 text-3xl font-black text-slate-950">Hỏi AI trước khi đến quầy</h3>
                        <p class="mt-3 text-base font-medium leading-7 text-slate-600">AI có thể giải thích quy trình, gợi ý khoa phù hợp và nhắc các dấu hiệu cần ưu tiên. Kết quả chỉ mang tính tham khảo.</p>
                    </div>
                @endif

                @foreach ($messages as $index => $item)
                    <div class="flex justify-end">
                        <div class="max-w-[82%] rounded-[1.5rem] bg-gradient-to-br from-cyan-500 to-blue-500 px-5 py-4 text-sm font-bold leading-7 text-white shadow-[0_18px_45px_rgba(14,165,233,0.18)]">
                            {{ $item['question'] }}
                        </div>
                    </div>

                    <div id="{{ $loop->last ? 'latest-answer' : '' }}" class="flex gap-3">
                        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-white shadow-sm">
                            <x-brand-mark class="h-8 w-8" />
                        </div>
                        <div class="max-w-[88%] rounded-[1.5rem] border border-white/80 bg-white/95 p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                            <div id="ai-response-{{ $index }}" data-markdown class="prose prose-slate max-w-none prose-headings:font-black">{{ $item['answer'] }}</div>
                            <div class="mt-4 rounded-2xl bg-amber-50 p-4 text-sm font-bold leading-6 text-amber-800">
                                Lưu ý: AI không thay thế bác sĩ. Nếu triệu chứng nặng hoặc kéo dài, hãy liên hệ nhân viên y tế.
                            </div>
                            @if (! empty($item['sources']))
                                <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                                    <p class="font-black text-slate-900">Nguồn tham chiếu</p>
                                    <ul class="mt-2 space-y-2 list-disc pl-5">
                                        @foreach($item['sources'] as $source)
                                            <li>{{ $source }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <button type="button" onclick="window.copyAiResponse('ai-response-{{ $index }}')" class="mt-4 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-600 transition hover:border-cyan-300 hover:text-cyan-700">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                Sao chép
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-white/80 bg-white/90 p-4">
                <div class="mb-3 flex gap-2 overflow-x-auto pb-1">
                    @foreach ($prompts as $prompt)
                        <button type="button" onclick="document.getElementById('question-input').value = @js($prompt)" class="shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 shadow-sm transition hover:border-violet-300 hover:text-violet-700">
                            {{ $prompt }}
                        </button>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('ai-chat.store') }}" class="flex gap-3" onsubmit="document.getElementById('ai-typing').classList.remove('hidden')">
                    @csrf
                    <textarea id="question-input" class="field min-h-14 flex-1 resize-none py-4" name="question" rows="1" required placeholder="Nhập câu hỏi về triệu chứng, giấy tờ hoặc hàng đợi...">{{ old('question', $question) }}</textarea>
                    <button class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-slate-950 text-white shadow-lg transition hover:-translate-y-0.5" type="submit" aria-label="Gửi câu hỏi">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </button>
                </form>
                <div id="ai-typing" class="hidden pt-3 text-sm font-bold text-violet-700">
                    AI đang phân tích<span class="animate-pulse">...</span>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
