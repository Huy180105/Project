@extends('layouts.app')

@section('title', 'Documents')

@section('content')
    <section class="max-w-2xl rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-base font-semibold">Upload study or wellness document</h2>
        <p class="mt-2 text-sm text-slate-600">PDF, DOCX, and TXT files are parsed, chunked, embedded, stored in Pinecone, and logged in MongoDB by the gateway.</p>
        <form class="mt-5 space-y-4" method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
            @csrf
            <label class="block text-sm font-medium">
                Title
                <input class="field mt-1" type="text" name="title" placeholder="Exam plan or focus notes">
            </label>
            <label class="block text-sm font-medium">
                File
                <input class="field mt-1" type="file" name="document" accept=".pdf,.docx,.txt" required>
            </label>
            <button class="btn-primary" type="submit">Upload and ingest</button>
        </form>
    </section>
@endsection
