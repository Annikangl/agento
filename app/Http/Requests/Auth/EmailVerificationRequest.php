<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiRequest;

/**
 * @property string $email
 */
class EmailVerificationRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email']
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email already exists',
        ];
    }
}
