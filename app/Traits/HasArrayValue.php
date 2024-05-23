<?php

namespace App\Traits;

trait HasArrayValue
{
    public static function values(): array
    {
        return collect(self::cases())->map(fn($item) => $item->value)->toArray();
    }
}
