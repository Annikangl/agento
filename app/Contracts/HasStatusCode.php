<?php

namespace App\Contracts;

interface HasStatusCode
{
    public function getStatusCode(): int;
}
