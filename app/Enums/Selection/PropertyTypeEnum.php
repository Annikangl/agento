<?php

namespace App\Enums\Selection;


use App\Traits\HasArrayValue;

enum PropertyTypeEnum: string
{
    use HasArrayValue;

    case APARTMENT = 'apartment';
    case VILLA = 'villa';
    case TOWNHOUSE = 'townhouse';
    case PENTHOUSE = 'penthouse';


}
