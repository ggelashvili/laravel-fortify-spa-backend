<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

Route::middleware('guest')->group(
    function () {
        $limiter = config('fortify.limiters.login');

        Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware(
            array_filter([$limiter ? 'throttle:' . $limiter : null])
        );

        Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store']);

        if (Features::enabled(Features::registration())) {
            Route::post('/register', [RegisteredUserController::class, 'store']);
        }

        if (Features::enabled(Features::resetPasswords())) {
            Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
            Route::post('/reset-password', [NewPasswordController::class, 'store']);
        }
    }
);

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/me', [UserController::class, 'me']);
        Route::get('/tickets', [TicketController::class, 'index']);

        Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store']);
        Route::get('/user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show']);

        // Two Factor Authentication...
        if (Features::enabled(Features::twoFactorAuthentication())) {
            $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
                ? ['password.confirm']
                : [];

            Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
                ->middleware($twoFactorMiddleware);

            Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
                ->middleware($twoFactorMiddleware);

            Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
                ->middleware($twoFactorMiddleware);

            Route::get('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
                ->middleware($twoFactorMiddleware);

            Route::post('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])
                ->middleware($twoFactorMiddleware);
        }

        if (Features::enabled(Features::updateProfileInformation())) {
            Route::put('/user/profile-information', [ProfileInformationController::class, 'update']);
        }

        if (Features::enabled(Features::updatePasswords())) {
            Route::put('/user/password', [PasswordController::class, 'update']);
        }

//        // Email Verification...
//        if (Features::enabled(Features::emailVerification())) {
//            Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
//                ->middleware(['auth'])
//                ->name('verification.notice');
//
//            Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
//                ->middleware(['auth', 'signed', 'throttle:6,1'])
//                ->name('verification.verify');
//
//            Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//                ->middleware(['auth', 'throttle:6,1'])
//                ->name('verification.send');
//        }
    }
);
