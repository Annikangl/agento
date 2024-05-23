<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\User;

use App\MoonShine\Components\CommercialOffersList;
use MoonShine\Pages\Crud\DetailPage;

class UserDetailPage extends DetailPage
{

    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer(),
            CommercialOffersList::make($this->getResource()->getItem()),
        ];
    }

}
