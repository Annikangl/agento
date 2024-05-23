<?php

namespace App\Enums\Selection;

use App\Traits\HasArrayValue;

enum CompletionEnum: string
{
    use HasArrayValue;

    case OFF_PLAN = 'off_plan';
    case READY = 'ready';
    case ANY = 'any';
}
