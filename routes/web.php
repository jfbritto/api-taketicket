<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\Dashboard\DashboardController;
use App\Http\Controllers\Web\Dashboard\DashboardEventController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\PublicEventController;
use App\Http\Middleware\EnsureHasOrganizer;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('event/{slug}', [PublicEventController::class, 'show'])->name('event.show');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    // Dashboard onboarding (auth but NO organizer middleware)
    Route::get('dashboard/onboarding', [DashboardController::class, 'onboarding'])->name('dashboard.onboarding');
    Route::post('dashboard/onboarding', [DashboardController::class, 'storeOrganizer'])->name('dashboard.storeOrganizer');

    // Dashboard (auth + organizer required)
    Route::prefix('dashboard')->middleware(EnsureHasOrganizer::class)->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Event CRUD
        Route::get('events', [DashboardEventController::class, 'index'])->name('dashboard.events');
        Route::get('events/create', [DashboardEventController::class, 'create'])->name('dashboard.events.create');
        Route::post('events', [DashboardEventController::class, 'store'])->name('dashboard.events.store');
        Route::get('events/{event}/edit', [DashboardEventController::class, 'edit'])->name('dashboard.events.edit');
        Route::put('events/{event}', [DashboardEventController::class, 'update'])->name('dashboard.events.update');
        Route::patch('events/{event}/publish', [DashboardEventController::class, 'publish'])->name('dashboard.events.publish');
        Route::patch('events/{event}/cancel', [DashboardEventController::class, 'cancel'])->name('dashboard.events.cancel');
        // More routes added in Tasks 7, 10
    });
});
