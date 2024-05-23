<?php

namespace App\DTOs\Offer;

use App\Rules\SourceValidUrl;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class CommercialOfferCreateDto extends ValidatedDTO
{
    public string $source_link;
    public string $lang;

    protected function rules(): array
    {
        return [
            'source_link' => ['required', 'string', new SourceValidUrl()],
            'lang' => ['required', 'string', 'in:ru,en'],
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [
        ];
    }
}
