<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OrganizerController;
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
});
