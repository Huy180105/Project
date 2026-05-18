<?php

namespace App\Services;

class RagService
{
    public function __construct(
        private readonly OpenAiClient $openAi,
        private readonly PineconeClient $pinecone,
        private readonly MongoMemoryStore $memory,
        private readonly TextChunker $chunker,
    ) {
    }

    public function ingestDocument(string $title, string $text, string $source = 'manual-upload'): array
    {
        $chunks = $this->chunker->chunk($text);
        $vectors = [];

        foreach ($chunks as $index => $chunk) {
            $vectors[] = [
                'id' => $this->vectorId($title, $index),
                'values' => $this->openAi->embedding($chunk),
                'metadata' => [
                    'title' => $title,
                    'source' => $source,
                    'chunk_index' => $index,
                    'text' => $chunk,
                ],
            ];
        }

        $pineconeResult = $vectors === []
            ? ['status' => 'empty']
            : $this->pinecone->upsert($vectors);

        $this->memory->saveWorkflowLog('document_ingested', [
            'title' => $title,
            'source' => $source,
            'chunks' => count($chunks),
            'pinecone' => $pineconeResult,
        ]);

        return [
            'status' => 'ingested',
            'chunks' => count($chunks),
            'pinecone' => $pineconeResult,
        ];
    }

    public function answer(string $question, mixed $userId = null): array
    {
        $questionVector = $this->openAi->embedding($question);
        $matches = $this->pinecone->query($questionVector);
        $contexts = $this->contextsFromMatches($matches);

        $prompt = $this->prompt($question, $contexts);
        $answer = $this->openAi->complete($prompt);

        $sources = array_map(fn (array $match): array => [
            'id' => $match['id'] ?? null,
            'score' => $match['score'] ?? null,
            'title' => $match['metadata']['title'] ?? null,
            'source' => $match['metadata']['source'] ?? null,
        ], $matches);

        $this->memory->saveChat((string) $userId, $question, $answer, $sources);

        return [
            'answer' => $answer,
            'sources' => $sources,
        ];
    }

    private function contextsFromMatches(array $matches): array
    {
        return array_values(array_filter(array_map(
            fn (array $match): ?string => $match['metadata']['text'] ?? null,
            $matches,
        )));
    }

    private function prompt(string $question, array $contexts): string
    {
        $contextText = $contexts === []
            ? 'No vector context was returned. Answer with a clear setup reminder.'
            : implode("\n\n---\n\n", $contexts);

        return <<<PROMPT
You are StudyWell, the AI assistant for a student focus and wellness platform.
Use the retrieved context when it is relevant.
Connect study habits, focus blocks, sleep, screen time, mood, and uploaded notes.
Do not invent diagnoses. Give practical, non-medical guidance and suggest professional support when the user describes severe distress.

Retrieved context:
{$contextText}

User question:
{$question}
PROMPT;
    }

    private function vectorId(string $title, int $index): string
    {
        return sha1($title.'-'.$index.'-'.microtime(true));
    }
}
