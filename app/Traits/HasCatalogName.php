<?php

namespace App\Traits;

use App\Models\Catalogs\Property\CatalogProperty;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasCatalogName
{
    /**
     * Defina cast atribute catalogName
     * @return Attribute
     */
    public function catalogName(): Attribute
    {
        $attribute = new Attribute();

        if (static::getModel() instanceof CatalogProperty) {
            return $attribute->get(
               fn() => 'propertyfinder'
            );
        }

        return $attribute;
    }
}
