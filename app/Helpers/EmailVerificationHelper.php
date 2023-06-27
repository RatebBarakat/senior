<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class EmailVerificationHelper
{
    static public function verificationUrl($notifiable)
    {
        $expires = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60));
        $email = $notifiable->getEmailForVerification();
        $secret = env('APP_KEY');
        $token = hash_hmac('sha256', "$email", $secret);

        return URL::temporarySignedRoute(
            'verification.verify',
            $expires,
            [
                'id' => $notifiable->getKey(),
                'expiry' => $expires->getTimestamp(),
                'token' => $token,
            ]
        );
    }
}
