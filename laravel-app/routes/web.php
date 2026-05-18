<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DoctorQueueController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\NotificationLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueTicketController;
use App\Http\Controllers\ReceptionQueueController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/display/{department}', DisplayController::class)->name('display.department');
Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk.index');
Route::post('/kiosk/tickets', [KioskController::class, 'store'])->name('kiosk.tickets.store');
Route::get('/kiosk/tickets/{ticket}', [KioskController::class, 'show'])->name('kiosk.tickets.show');
Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('ai-chat', [AiChatController::class, 'index'])->name('ai-chat.index');
    Route::post('ai-chat', [AiChatController::class, 'store'])->name('ai-chat.store');
    Route::delete('ai-chat/history', [AiChatController::class, 'destroy'])->name('ai-chat.history.destroy');

    Route::get('queue-tickets', [QueueTicketController::class, 'index'])->name('queue-tickets.index');
    Route::get('queue-tickets/create', [QueueTicketController::class, 'create'])->name('queue-tickets.create');
    Route::get('queue-tickets/status', [QueueTicketController::class, 'status'])->name('queue-tickets.status');
    Route::post('queue-tickets', [QueueTicketController::class, 'store'])->name('queue-tickets.store');
    Route::post('queue-tickets/call-next/{department}', [QueueTicketController::class, 'callNext'])->name('queue-tickets.call-next');
    Route::patch('queue-tickets/{ticket}/payment', [QueueTicketController::class, 'activatePayment'])->name('queue-tickets.payment');
    Route::patch('queue-tickets/{ticket}/serving', [QueueTicketController::class, 'serving'])->name('queue-tickets.serving');
    Route::patch('queue-tickets/{ticket}/missed', [QueueTicketController::class, 'missed'])->name('queue-tickets.missed');
    Route::patch('queue-tickets/{ticket}/recall', [QueueTicketController::class, 'recall'])->name('queue-tickets.recall');
    Route::patch('queue-tickets/{ticket}/complete', [QueueTicketController::class, 'complete'])->name('queue-tickets.complete');
    Route::patch('queue-tickets/{ticket}/cancel', [QueueTicketController::class, 'cancel'])->name('queue-tickets.cancel');

    Route::prefix('doctor')->name('doctor.')->group(function () {
        Route::get('queue', [DoctorQueueController::class, 'index'])->name('queue.index');
        Route::post('queue/call-next', [DoctorQueueController::class, 'callNext'])->name('queue.call-next');
        Route::patch('queue/{ticket}/serving', [DoctorQueueController::class, 'markServing'])->name('queue.serving');
        Route::patch('queue/{ticket}/missed', [DoctorQueueController::class, 'markMissed'])->name('queue.missed');
        Route::patch('queue/{ticket}/recall', [DoctorQueueController::class, 'recall'])->name('queue.recall');
        Route::patch('queue/{ticket}/complete', [DoctorQueueController::class, 'complete'])->name('queue.complete');
    });

    Route::prefix('reception')->name('reception.')->group(function () {
        Route::get('queue', [ReceptionQueueController::class, 'index'])->name('queue.index');
        Route::get('queue/create', [ReceptionQueueController::class, 'create'])->name('queue.create');
        Route::post('queue', [ReceptionQueueController::class, 'store'])->name('queue.store');
        Route::patch('queue/{ticket}/activate', [ReceptionQueueController::class, 'activate'])->name('queue.activate');
        Route::patch('queue/{ticket}/cancel', [ReceptionQueueController::class, 'cancel'])->name('queue.cancel');
    });

    Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('notifications', NotificationLogController::class)->name('notifications.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
