<?php

namespace App\Http\Requests\Selection;

use App\Http\Requests\ApiRequest;

class CreateAdvertsRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'selection_id' => ['required', 'exists:selections,id'],
            'added_catalog_items' => ['required', 'array','max:50'],
            'added_catalog_items.*.catalog_item_id' => ['required', 'int'],
            'added_catalog_items.*.catalog_name' => ['required', 'string', 'in:propertyfinder,dubizzle,bayut'],
        ];
    }
}
