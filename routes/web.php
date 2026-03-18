<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\Dashboard\CheckinController;
use App\Http\Controllers\Web\Dashboard\CollaboratorController;
use App\Http\Controllers\Web\Dashboard\DashboardController;
use App\Http\Controllers\Web\Dashboard\FinancialController;
use App\Http\Controllers\Web\Dashboard\DashboardEventController;
use App\Http\Controllers\Web\Dashboard\OrderController as DashboardOrderController;
use App\Http\Controllers\Web\Dashboard\GlobalParticipantController;
use App\Http\Controllers\Web\Dashboard\ParticipantController;
use App\Http\Controllers\Web\Dashboard\SettingsController;
use App\Http\Controllers\Web\Dashboard\TicketController as DashboardTicketController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\InvitationController;
use App\Http\Controllers\Web\MyTicketsController;
use App\Http\Controllers\Web\PublicEventController;
use App\Http\Controllers\Web\Staff\StaffController;
use App\Http\Controllers\Web\Staff\StaffCheckinController;
use App\Http\Controllers\Web\Staff\StaffParticipantController;
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
    // My Tickets routes
    Route::get('my-tickets', [MyTicketsController::class, 'index'])->name('my-tickets');
    Route::get('my-tickets/{ticket}', [MyTicketsController::class, 'show'])->name('my-tickets.show');

    // Checkout routes
    Route::post('checkout/order', [CheckoutController::class, 'createOrder'])->name('checkout.order');
    Route::get('checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    Route::get('checkout/{order}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('checkout/{order}', [CheckoutController::class, 'saveParticipants'])->name('checkout.participants');
    Route::get('checkout/{order}/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('checkout/{order}/payment', [CheckoutController::class, 'processPayment'])->name('checkout.processPayment');
    Route::get('checkout/{order}/status', [CheckoutController::class, 'status'])->name('checkout.status');

    // Staff routes
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');

        Route::middleware('ensure.collaborator')->group(function () {
            Route::get('events/{event}/checkin', [StaffCheckinController::class, 'index'])->name('checkin');
            Route::post('events/{event}/checkin/validate', [StaffCheckinController::class, 'validateTicket'])->name('checkin.validate');
            Route::post('events/{event}/checkin/undo', [StaffCheckinController::class, 'undo'])->name('checkin.undo');
            Route::get('events/{event}/participants', [StaffParticipantController::class, 'index'])->name('participants');
        });
    });

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
        Route::get('events/{event}', [DashboardEventController::class, 'show'])->name('dashboard.events.show');
        Route::get('events/{event}/edit', [DashboardEventController::class, 'edit'])->name('dashboard.events.edit');
        Route::put('events/{event}', [DashboardEventController::class, 'update'])->name('dashboard.events.update');
        Route::patch('events/{event}/publish', [DashboardEventController::class, 'publish'])->name('dashboard.events.publish');
        Route::patch('events/{event}/cancel', [DashboardEventController::class, 'cancel'])->name('dashboard.events.cancel');
        // Orders, Participants, Tickets
        Route::get('events/{event}/orders', [DashboardOrderController::class, 'index'])->name('dashboard.orders');
        Route::get('events/{event}/orders/{order}', [DashboardOrderController::class, 'show'])->name('dashboard.orders.show');
        Route::get('events/{event}/participants', [ParticipantController::class, 'index'])->name('dashboard.participants');
        Route::get('events/{event}/participants/export', [ParticipantController::class, 'export'])->name('dashboard.participants.export');
        Route::get('events/{event}/tickets', [DashboardTicketController::class, 'index'])->name('dashboard.tickets');

        // Check-in
        Route::get('checkin', [CheckinController::class, 'index'])->name('dashboard.checkin');
        Route::post('checkin/validate', [CheckinController::class, 'validateTicket'])->name('dashboard.checkin.validate');
        Route::post('checkin/undo', [CheckinController::class, 'undo'])->name('dashboard.checkin.undo');

        // Financial
        Route::get('financeiro', [FinancialController::class, 'index'])->name('dashboard.financeiro');

        // Global Participants
        Route::get('participantes', [GlobalParticipantController::class, 'index'])->name('dashboard.participantes');
        Route::get('participantes/export', [GlobalParticipantController::class, 'export'])->name('dashboard.participantes.export');

        // Settings
        Route::get('settings', [SettingsController::class, 'index'])->name('dashboard.settings');
        Route::put('settings/organizer', [SettingsController::class, 'updateOrganizer'])->name('dashboard.settings.organizer');
        Route::put('settings/password', [SettingsController::class, 'updatePassword'])->name('dashboard.settings.password');

        // Collaborators
        Route::post('events/{event}/collaborators', [CollaboratorController::class, 'store'])->name('dashboard.collaborators.store');
        Route::delete('events/{event}/collaborators/{collaborator}', [CollaboratorController::class, 'destroy'])->name('dashboard.collaborators.destroy');
    });
});

Route::middleware('signed')->get('invitation/{collaborator}', [InvitationController::class, 'show'])->name('invitation.accept');
