<?php

namespace App\Http\Requests;


class PromoCodeRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'exists:promocodes,code']
        ];
    }
}
