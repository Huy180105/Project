<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GatewayClient
{
    public function chat(string $question, ?int $userId = null): array
    {
        return $this->post('/api/ai/chat', [
            'question' => $question,
            'user_id' => $userId,
        ], [
            'answer' => 'AI gateway unavailable. Start lumen-api to get a RAG response.',
            'sources' => [],
        ]);
    }

    public function ingestDocument(array $payload): array
    {
        return $this->post('/api/documents/ingest', $payload, [
            'status' => 'queued',
            'chunks' => 0,
        ]);
    }

    public function wellnessInsights(array $signals): array
    {
        return $this->post('/api/wellness/insights', ['signals' => $signals], [
            'summary' => 'The AI gateway is offline. Start the Lumen API to generate study-life insights.',
        ]);
    }

    private function post(string $path, array $payload, array $fallback): array
    {
        try {
            $response = Http::timeout((int) config('services.lumen_api.timeout', 30))
                ->acceptJson()
                ->post($this->baseUrl().$path, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Lumen gateway returned an error.', [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Lumen gateway request failed.', [
                'path' => $path,
                'message' => $exception->getMessage(),
            ]);
        }

        return $fallback;
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.lumen_api.url'), '/');
    }
}
