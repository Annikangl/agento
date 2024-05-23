<?php

namespace App\Models\Catalogs\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogPropertyImg extends Model
{
    use HasFactory;

    protected $table = 'catalog_property_img';

    protected $primaryKey = 'img_id';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'type',
        'path',
    ];
}
