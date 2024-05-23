<?php

namespace App\Pipes\User;

use App\Models\User\User;
use App\Models\User\VerificationCode;

class ClearVerificationCode
{
    public function handle(User $user, \Closure $next)
    {
        VerificationCode::clearVerificationCode($user->email);

        return $next($user);
    }
}
