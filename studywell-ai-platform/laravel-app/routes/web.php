<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\WellnessSignalController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::resource('wellness-signals', WellnessSignalController::class)
    ->only(['index', 'store', 'destroy'])
    ->parameters(['wellness-signals' => 'wellnessSignal']);

Route::get('ai-chat', [AiChatController::class, 'index'])->name('ai-chat.index');
Route::post('ai-chat', [AiChatController::class, 'store'])->name('ai-chat.store');

Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
