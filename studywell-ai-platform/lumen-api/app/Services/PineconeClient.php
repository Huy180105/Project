<?php

namespace App\Services;

use GuzzleHttp\Client;
use Throwable;

class PineconeClient
{
    public function __construct(private readonly ?Client $client = null)
    {
    }

    public function upsert(array $vectors): array
    {
        if (! $this->configured()) {
            return ['status' => 'skipped', 'reason' => 'pinecone_not_configured'];
        }

        try {
            $response = $this->http()->post($this->host().'/vectors/upsert', [
                'headers' => $this->headers(),
                'json' => [
                    'namespace' => env('PINECONE_NAMESPACE', 'studywell'),
                    'vectors' => $vectors,
                ],
            ]);

            return json_decode((string) $response->getBody(), true) ?: ['status' => 'ok'];
        } catch (Throwable $exception) {
            return ['status' => 'failed', 'message' => $exception->getMessage()];
        }
    }

    public function query(array $vector, int $topK = 5): array
    {
        if (! $this->configured()) {
            return [];
        }

        try {
            $response = $this->http()->post($this->host().'/query', [
                'headers' => $this->headers(),
                'json' => [
                    'namespace' => env('PINECONE_NAMESPACE', 'studywell'),
                    'vector' => $vector,
                    'topK' => $topK,
                    'includeMetadata' => true,
                ],
            ]);

            $payload = json_decode((string) $response->getBody(), true);

            return $payload['matches'] ?? [];
        } catch (Throwable) {
            return [];
        }
    }

    private function configured(): bool
    {
        return (bool) env('PINECONE_API_KEY') && (bool) env('PINECONE_HOST');
    }

    private function headers(): array
    {
        return [
            'Api-Key' => env('PINECONE_API_KEY'),
            'Content-Type' => 'application/json',
        ];
    }

    private function host(): string
    {
        return rtrim((string) env('PINECONE_HOST'), '/');
    }

    private function http(): Client
    {
        return $this->client ?? new Client(['timeout' => 60]);
    }
}
