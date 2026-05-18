<?php

namespace App\Services;

use GuzzleHttp\Client;
use Throwable;

class OpenAiClient
{
    public function __construct(private readonly ?Client $client = null)
    {
    }

    public function complete(string $prompt): string
    {
        if (! $this->apiKey()) {
            return $this->mockCompletion($prompt);
        }

        try {
            $response = $this->http()->post('https://api.openai.com/v1/responses', [
                'headers' => $this->headers(),
                'json' => [
                    'model' => env('OPENAI_CHAT_MODEL', 'gpt-5.4-mini'),
                    'input' => $prompt,
                ],
            ]);

            $payload = json_decode((string) $response->getBody(), true);

            return $payload['output_text']
                ?? $payload['output'][0]['content'][0]['text']
                ?? 'The model returned an empty response.';
        } catch (Throwable $exception) {
            return 'OpenAI request failed: '.$exception->getMessage();
        }
    }

    public function embedding(string $text): array
    {
        if (! $this->apiKey()) {
            return $this->mockEmbedding($text);
        }

        try {
            $response = $this->http()->post('https://api.openai.com/v1/embeddings', [
                'headers' => $this->headers(),
                'json' => [
                    'model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
                    'input' => $text,
                    'encoding_format' => 'float',
                ],
            ]);

            $payload = json_decode((string) $response->getBody(), true);

            return $payload['data'][0]['embedding'] ?? $this->mockEmbedding($text);
        } catch (Throwable) {
            return $this->mockEmbedding($text);
        }
    }

    private function apiKey(): ?string
    {
        $key = env('OPENAI_API_KEY');

        return is_string($key) && $key !== '' ? $key : null;
    }

    private function headers(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->apiKey(),
            'Content-Type' => 'application/json',
        ];
    }

    private function http(): Client
    {
        return $this->client ?? new Client(['timeout' => 60]);
    }

    private function mockCompletion(string $prompt): string
    {
        if (str_contains(strtolower($prompt), 'daily study-life signals')) {
            return 'Mock insight: your focus blocks work best when sleep and mood stay stable. Try scheduling deep work after your highest-energy period and reduce late screen time before heavy study days.';
        }

        return 'Mock RAG answer: configure OPENAI_API_KEY, PINECONE_HOST, and PINECONE_API_KEY to generate grounded answers from your study notes and wellness documents.';
    }

    private function mockEmbedding(string $text): array
    {
        $hash = hash('sha256', $text);
        $values = [];

        for ($i = 0; $i < 64; $i++) {
            $pair = substr($hash, ($i * 2) % 64, 2);
            $values[] = (hexdec($pair) / 255) * 2 - 1;
        }

        return $values;
    }
}
