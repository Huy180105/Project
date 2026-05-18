@extends('layouts.app')

@section('title', 'AI Chat RAG')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[420px_1fr]">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold">Ask about study patterns or uploaded notes</h2>
            <form class="mt-4 space-y-4" method="POST" action="{{ route('ai-chat.store') }}">
                @csrf
                <textarea class="field" name="question" rows="8" required placeholder="Example: How should I adjust focus blocks based on my sleep and mood?">{{ old('question', $question) }}</textarea>
                <button class="btn-primary w-full" type="submit">Send question</button>
            </form>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold">Answer</h2>
            @if ($answer)
                <p class="mt-4 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $answer }}</p>
                @if (count($sources))
                    <div class="mt-6">
                        <h3 class="text-sm font-semibold">Sources</h3>
                        <ul class="mt-2 space-y-2 text-sm text-slate-600">
                            @foreach ($sources as $source)
                                <li class="rounded-md bg-slate-50 px-3 py-2">{{ $source['title'] ?? $source['id'] ?? 'Context chunk' }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @else
                <p class="mt-4 text-sm text-slate-500">Submit a question to call the Lumen RAG gateway.</p>
            @endif
        </section>
    </div>
@endsection
