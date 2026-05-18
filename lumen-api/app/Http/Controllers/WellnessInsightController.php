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
                'summary' => 'No health logs yet. Add sleep, hydration, heart rate, symptoms, mood, and calorie records to generate an insight.',
            ]);
        }

        $prompt = "You are Health AI Platform, a non-diagnostic health assistant. Review these health log records and provide a concise pattern summary with 2 practical suggestions. Avoid diagnosis and recommend professional medical support for severe or persistent symptoms.\n\n".
            json_encode($signals, JSON_PRETTY_PRINT);

        return response()->json([
            'summary' => $openAi->complete($prompt),
        ]);
    }
}
