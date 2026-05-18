<?php

return [
    'lumen_api' => [
        'url' => env('LUMEN_API_URL', 'http://127.0.0.1:8081'),
        'timeout' => env('LUMEN_API_TIMEOUT', 30),
    ],
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'chat_model' => env('OPENAI_CHAT_MODEL', 'gpt-5.4-mini'),
        'embedding_model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
    ],
    'pinecone' => [
        'api_key' => env('PINECONE_API_KEY'),
        'host' => env('PINECONE_HOST'),
        'index' => env('PINECONE_INDEX', 'studywell-ai-platform'),
    ],
];
