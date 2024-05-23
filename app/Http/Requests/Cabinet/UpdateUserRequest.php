<?php

namespace App\Http\Requests\Cabinet;

use App\Http\Requests\ApiRequest;

/**
 * @property string|null $name
 */
class UpdateUserRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
