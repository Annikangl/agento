<?php

namespace App\Http\Requests\Selection;

use App\Enums\Selection\AddressType;
use App\Enums\Selection\CompletionEnum;
use App\Enums\Selection\DealTypeEnum;
use App\Enums\Selection\PropertyTypeEnum;
use App\Enums\Selection\SelectionSizeUnit;
use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;


/**
 * @property string title
 * @property string deal_type
 * @property string property_type
 * @property string completion
 * @property array beds
 * @property int size_from
 * @property int size_to
 * @property string size_units
 * @property string location
 * @property string location_type
 * @property int budget_from
 * @property int budget_to
 */
class CreateSelectionRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'location_type' => strtoupper($this->location_type),
            'size_units' => $this->size_units ? strtolower($this->size_units) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'deal_type' => ['required', Rule::enum(DealTypeEnum::class)],
            'property_type' => ['required', Rule::enum(PropertyTypeEnum::class)],
            'completion' => ['required', Rule::enum(CompletionEnum::class)],
            'beds' => ['required', 'array'],
            'size_from' => ['nullable', 'integer'],
            'size_to' => ['nullable', 'integer'],
            'size_units' => ['nullable', 'required_with:size_from', Rule::enum(SelectionSizeUnit::class)],
            'location' => ['required', 'string', 'max:255'],
            'location_type' => ['required', Rule::enum(AddressType::class)],
            'budget_from' => ['required', 'integer', 'max_digits:9'],
            'budget_to' => ['required', 'integer', 'max_digits:9'],
        ];
    }

}
