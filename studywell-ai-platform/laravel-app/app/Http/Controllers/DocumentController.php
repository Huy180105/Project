<?php

namespace App\Http\Controllers;

use App\Services\DocumentTextExtractor;
use App\Services\GatewayClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(): View
    {
        return view('documents.index');
    }

    public function store(
        Request $request,
        DocumentTextExtractor $extractor,
        GatewayClient $gateway,
    ): RedirectResponse {
        $data = $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,docx,txt', 'max:20480'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $data['document'];
        $text = $extractor->extract($file);

        if (trim($text) === '') {
            return back()->withErrors(['document' => 'No text could be extracted from this file.']);
        }

        $result = $gateway->ingestDocument([
            'title' => $data['title'] ?: $file->getClientOriginalName(),
            'source' => 'laravel-upload',
            'text' => $text,
        ]);

        return back()->with('status', sprintf(
            'Document queued for RAG ingestion. Chunks: %s',
            $result['chunks'] ?? 'pending',
        ));
    }
}

