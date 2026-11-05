<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ScreenController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/screens/{screen}', [ScreenController::class, 'show'])->name('screens.show');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::patch('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
    Route::patch('/payments/{assignment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
