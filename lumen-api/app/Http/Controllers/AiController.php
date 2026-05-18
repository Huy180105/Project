<?php

namespace App\Http\Controllers;

use App\Services\RagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function chat(Request $request, RagService $rag): JsonResponse
    {
        $this->validate($request, [
            'question' => ['required', 'string', 'max:2000'],
            'user_id' => ['nullable'],
        ]);

        return response()->json($rag->answer(
            $request->input('question'),
            $request->input('user_id'),
        ));
    }
}

