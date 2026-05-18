<?php

/** @var Laravel\Lumen\Routing\Router $router */

$router->get('/status', function () {
    return response()->json([
        'service' => 'lumen-api',
        'status' => 'ok',
        'time' => now()->toIso8601String(),
    ]);
});

$router->post('/api/ai/chat', 'AiController@chat');
$router->post('/api/documents/ingest', 'DocumentController@ingest');
$router->post('/api/wellness/insights', 'WellnessInsightController@summarize');
