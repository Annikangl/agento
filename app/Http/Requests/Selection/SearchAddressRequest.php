<?php

namespace App\Http\Requests\Selection;

use App\Http\Requests\ApiRequest;

/**
 * @property string $name
 * @property int|null $take
 */
class SearchAddressRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'take' => ['nullable', 'int']
        ];
    }
}
