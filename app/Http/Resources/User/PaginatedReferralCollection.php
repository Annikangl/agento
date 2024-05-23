<?php

namespace App\Http\Resources\User;

use App\Http\Resources\PaginateCollection;

class PaginatedReferralCollection extends PaginateCollection
{
    public $collects = ReferralResource::class;
}
