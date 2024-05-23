<?php

namespace App\Enums\Selection;

use App\Traits\HasArrayValue;

enum DealTypeEnum: string
{
    use HasArrayValue;

    case BUE = 'buy';
    case RENT = 'rent';
}
