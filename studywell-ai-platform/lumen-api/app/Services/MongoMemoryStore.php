<?php

namespace App\Services;

use MongoDB\Client;
use Throwable;

class MongoMemoryStore
{
    public function saveChat(?string $userId, string $question, string $answer, array $sources): void
    {
        $this->insert('chat_history', [
            'user_id' => $userId,
            'question' => $question,
            'answer' => $answer,
            'sources' => $sources,
            'created_at' => new \MongoDB\BSON\UTCDateTime(),
        ]);
    }

    public function saveWorkflowLog(string $type, array $payload): void
    {
        $this->insert('workflow_logs', [
            'type' => $type,
            'payload' => $payload,
            'created_at' => new \MongoDB\BSON\UTCDateTime(),
        ]);
    }

    private function insert(string $collection, array $document): void
    {
        if (! class_exists(Client::class) || ! env('MONGO_URI')) {
            return;
        }

        try {
            $client = new Client(env('MONGO_URI'));
            $database = $client->selectDatabase(env('MONGO_DATABASE', 'studywell_memory'));
            $database->selectCollection($collection)->insertOne($document);
        } catch (Throwable) {
            //
        }
    }
}
