<?php

namespace App\Http\Resources\Selection\Advert;

use App\Http\Resources\PaginateCollection;

class PaginaterAdvertCollection extends PaginateCollection
{
    public $collects = ShowAdvertResource::class;
}
