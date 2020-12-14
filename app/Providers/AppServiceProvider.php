<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $spaDomain = config('sanctum.stateful', [])[0];

        ResetPassword::createUrlUsing(
            function ($notifiable, $token) use ($spaDomain) {
                return "http://{$spaDomain}/reset-password/{$token}?email={$notifiable->getEmailForPasswordReset()}";
            }
        );

        VerifyEmail::createUrlUsing(
            function ($notifiable) use ($spaDomain) {
                $url = URL::temporarySignedRoute(
                    'verification.verify',
                    Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                    [
                        'id'   => $notifiable->getKey(),
                        'hash' => sha1($notifiable->getEmailForVerification()),
                    ],
                    false
                );

                return "http://{$spaDomain}{$url}";
            }
        );
    }
}
