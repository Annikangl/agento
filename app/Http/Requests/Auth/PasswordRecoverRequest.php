<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiRequest;
use App\Rules\EmailVerificationCode;
use Illuminate\Validation\Rules\Password;

class PasswordRecoverRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::default()],
            'password_confirmation' => ['required', 'string'],
            'verification_code' => ['required', 'integer', 'digits:4', new EmailVerificationCode()],
        ];
    }
}
