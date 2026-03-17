<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CustomFieldController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\OrganizerController;
use App\Http\Controllers\API\OrganizerEventController;
use App\Http\Controllers\API\TicketTypeController;
use App\Http\Controllers\API\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('organizers', [OrganizerController::class, 'store']);
        Route::get('organizers/me', [OrganizerController::class, 'me']);
        Route::put('organizers/me', [OrganizerController::class, 'update']);
    });

    // Public event routes
    Route::get('events', [EventController::class, 'index']);
    Route::get('events/{slug}', [EventController::class, 'show']);

    // Organizer events (auth)
    Route::prefix('organizer/events')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [OrganizerEventController::class, 'index']);
        Route::post('/', [OrganizerEventController::class, 'store']);
        Route::get('{event}', [OrganizerEventController::class, 'show']);
        Route::put('{event}', [OrganizerEventController::class, 'update']);
        Route::patch('{event}/publish', [OrganizerEventController::class, 'publish']);
        Route::patch('{event}/cancel', [OrganizerEventController::class, 'cancel']);
    });

    // Ticket types and custom fields
    Route::prefix('organizer/events/{event}')->middleware('auth:sanctum')->group(function () {
        Route::post('ticket-types', [TicketTypeController::class, 'store']);
        Route::put('ticket-types/{ticketType}', [TicketTypeController::class, 'update']);
        Route::delete('ticket-types/{ticketType}', [TicketTypeController::class, 'destroy']);

        Route::get('custom-fields', [CustomFieldController::class, 'index']);
        Route::post('custom-fields', [CustomFieldController::class, 'store']);
        Route::put('custom-fields/{customField}', [CustomFieldController::class, 'update']);
        Route::delete('custom-fields/{customField}', [CustomFieldController::class, 'destroy']);
    });

    // Orders
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('orders', [OrderController::class, 'store']);
        Route::get('orders/my', [OrderController::class, 'myOrders']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
    });

    // Webhooks (public, no auth)
    Route::post('webhooks/asaas', [WebhookController::class, 'asaas']);
});
