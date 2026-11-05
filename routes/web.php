<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PublicActionController;
use App\Http\Controllers\ScreenController;
use App\Http\Controllers\UiActionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::get('/action/{token}', [PublicActionController::class, 'show'])->name('public-actions.show');
Route::post('/action/{token}', [PublicActionController::class, 'update'])->middleware('throttle:10,1')->name('public-actions.update');
Route::get('/action/{token}/complete', [PublicActionController::class, 'complete'])->name('public-actions.complete');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/screens/{screen}', [ScreenController::class, 'show'])->name('screens.show');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::patch('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::patch('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::patch('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
    Route::patch('/payments/{assignment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::post('/ui-actions/{action}', UiActionController::class)->name('ui-actions.store');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
