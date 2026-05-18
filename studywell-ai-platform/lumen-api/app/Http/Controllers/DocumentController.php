<?php

namespace App\Http\Controllers;

use App\Services\RagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function ingest(Request $request, RagService $rag): JsonResponse
    {
        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'text' => ['required', 'string'],
        ]);

        return response()->json($rag->ingestDocument(
            $request->input('title'),
            $request->input('text'),
            $request->input('source', 'manual-upload'),
        ));
    }
}

