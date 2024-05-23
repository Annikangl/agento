<?php

namespace App\Http\Requests\Offer;

use App\Http\Requests\ApiRequest;
use App\Rules\SourceValidUrl;

class CreateCommercialOfferRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'source_link' => ['required', 'string', new SourceValidUrl()],
            'lang' => ['required', 'string', 'in:ru,en'],
        ];
    }
}
