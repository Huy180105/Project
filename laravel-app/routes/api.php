<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QueueController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/departments', [QueueController::class, 'departments']);
Route::get('/display/{department}', [QueueController::class, 'display']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    Route::post('/tickets', [QueueController::class, 'store']);
    Route::get('/my-ticket', [QueueController::class, 'myTicket']);
    Route::get('/queue-status/{ticket}', [QueueController::class, 'status']);
    Route::get('/tickets/{ticket}/qr', [QueueController::class, 'qr']);
});
