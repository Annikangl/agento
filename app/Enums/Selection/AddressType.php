<?php

namespace App\Enums\Selection;

enum AddressType: string
{
    case CITY = 'CITY';
    case COMMUNITY = 'COMMUNITY';
    case SUBCOMMUNITY = 'SUBCOMMUNITY';
    case TOWER = 'TOWER';
}
