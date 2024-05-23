<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiRequest;
use App\Rules\EmailVerificationCode;
use App\Rules\PhoneNumber;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'country' => ['required', 'string', 'max:12', 'in:uae,usa'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => ['nullable', 'unique:users', new PhoneNumber()],
            'password' => ['required', 'confirmed', Password::default()],
            'password_confirmation' => ['required', 'string'],
            'verification_code' => ['required', 'integer', 'digits:4', ],
            'promocode' => ['nullable', 'string'],
            'fcm_token' => ['nullable', 'string'],
            'device_name' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => 'Phone number already exists',
            'email.unique' => 'Email already exists',
        ];
    }
}
