<?php

namespace App\Http\Controllers;

use App\Services\OpenAiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WellnessInsightController extends Controller
{
    public function summarize(Request $request, OpenAiClient $openAi): JsonResponse
    {
        $this->validate($request, [
            'signals' => ['array'],
        ]);

        $signals = $request->input('signals', []);

        if ($signals === []) {
            return response()->json([
                'summary' => 'No study-life signals yet. Add focus, sleep, mood, water, screen time, and reflection records to generate an insight.',
            ]);
        }

        $prompt = "You are StudyWell, a student productivity and wellness assistant. Review these daily study-life signals and provide a concise pattern summary with 2 practical suggestions. Avoid diagnosis and keep the advice student-friendly.\n\n".
            json_encode($signals, JSON_PRETTY_PRINT);

        return response()->json([
            'summary' => $openAi->complete($prompt),
        ]);
    }
}
