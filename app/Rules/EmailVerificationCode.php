<?php

namespace App\Rules;

use App\Exceptions\Api\Auth\VerificationCodeException;
use App\Models\User\VerificationCode;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class EmailVerificationCode implements ValidationRule
{
    /**
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!request()->exists('email')) {
            $fail('Email field is required');
            return;
        }

        $email = request()->get('email') ?: Auth::user()->email;

        if (strlen((string)abs($value)) !== 4 || !VerificationCode::checkVerificationCode($email, $value))  {
            $fail('Invalid verification code');
        }
    }
}
