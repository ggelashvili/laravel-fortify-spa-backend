<?php

use App\Http\Controllers\OtherBrowserSessionsController;
use App\Http\Controllers\TokenAuthController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(
    function () {
        $limiter = config('fortify.limiters.login');

        Route::post('/auth/token', [TokenAuthController::class, 'store'])->middleware(
            array_filter([$limiter ? 'throttle:' . $limiter : null])
        );
    }
);

Route::middleware('auth:sanctum')->group(
    function () {
        Route::delete('/auth/token', [TokenAuthController::class, 'destroy']);

        Route::get('/me', [UserController::class, 'me']);
        Route::get('/user/sessions', [OtherBrowserSessionsController::class, 'index']);
        Route::post('/user/sessions/purge', [OtherBrowserSessionsController::class, 'destroy']);

        Route::get('/tickets', [TicketController::class, 'index']);
    }
);
