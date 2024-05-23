<?php

namespace App\Enums\Selection;

use App\Traits\HasArrayValue;

enum SelectionSizeUnit: string
{
    use HasArrayValue;

    case SQFT = 'sqft';
    case SQM = 'sqm';
}
