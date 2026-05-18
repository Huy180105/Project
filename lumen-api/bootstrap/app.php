<?php

require_once __DIR__.'/../vendor/autoload.php';

(Dotenv\Dotenv::createImmutable(dirname(__DIR__)))->safeLoad();

$app = new Laravel\Lumen\Application(dirname(__DIR__));

$app->withFacades();
$app->withEloquent();

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router): void {
    require __DIR__.'/../routes/web.php';
});

return $app;

