<?php

namespace App\Http\Requests\Cabinet;

use App\Http\Requests\ApiRequest;
use App\Rules\EmailVerificationCode;
use Illuminate\Validation\Rules\Password;

class ChangeUserPasswordRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'new_password' => ['required', 'string', 'confirmed', Password::default()],
            'new_password_confirmation' => ['required', 'string'],
            'verification_code' => ['required', new EmailVerificationCode()]
        ];
    }
}
