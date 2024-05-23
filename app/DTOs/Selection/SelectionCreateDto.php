<?php

namespace App\DTOs\Selection;

use App\Enums\Selection\AddressType;
use App\Enums\Selection\CompletionEnum;
use App\Enums\Selection\DealTypeEnum;
use App\Enums\Selection\PropertyTypeEnum;
use App\Enums\Selection\SelectionSizeUnit;
use Illuminate\Validation\Rule;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class SelectionCreateDto extends ValidatedDTO
{
    public string $title;
    public string $deal_type;
    public string $property_type;
    public string $completion;
    public array $beds;
    public ?int $size_from;
    public ?int $size_to;
    public ?string $size_units;
    public string $location;
    public string $location_type;
    public int $budget_from;
    public int $budget_to;


    protected function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'deal_type' => ['required', Rule::enum(DealTypeEnum::class)],
            'property_type' => ['required', Rule::enum(PropertyTypeEnum::class)],
            'completion' => ['required', Rule::enum(CompletionEnum::class)],
            'beds' => ['required', 'array'],
            'size_from' => ['nullable', 'integer'],
            'size_to' => ['nullable', 'integer'],
            'size_units' => ['nullable', 'required_with:size_from', Rule::enum(SelectionSizeUnit::class)],
            'location' => ['required', 'string', 'max:255'],
            'budget_from' => ['required', 'integer'],
            'budget_to' => ['required', 'integer'],
            'location_type' => ['required', Rule::enum(AddressType::class)],
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [
            'budget_from' => new IntegerCast(),
            'budget_to' => new IntegerCast(),
            'size_from' => new IntegerCast(),
            'size_to' => new IntegerCast(),
        ];
    }
}
