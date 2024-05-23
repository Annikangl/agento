<?php

namespace App\UseCases\Selection;

use App\Exceptions\Api\Selection\AdvertException;
use App\Exceptions\Api\System\ModelNotFoundException;
use App\Models\Catalogs\Property\CatalogProperty;
use App\Models\Selection\Advert;
use App\Models\Selection\Selection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdvertService
{
    /**
     * Create adverts by selection
     * @param Collection $catalogItems
     * @param Selection $selection
     * @return void
     * @throws ModelNotFoundException
     */
    public function createBySelection(Collection $catalogItems, Selection $selection): void
    {
        $catalogItems->unique('catalog_item_id')->each(function ($catalogItem) use ($selection) {
           $catalogItem = $this->getCatalogItem($catalogItem['catalog_name'], $catalogItem['catalog_item_id']);

           $catalogItem->adverts()->create([
               'selection_id' => $selection->id,
           ]);
        });
    }

    /**
     * Get catalog item from other catalog models by catalog name
     * Now we have one model in the catalog, but in the future there may be more
     * @param string $name
     * @param int $catalogId
     * @return Model|CatalogProperty
     * @throws ModelNotFoundException
     */
    private function getCatalogItem(string $name, int $catalogId): Model|CatalogProperty
    {
        $catalogItem =  match ($name) {
            'propertyfinder' => CatalogProperty::query()->find($catalogId),
            default => null,
        };

        if (!$catalogItem) {
            throw new ModelNotFoundException('Catalog item not found', Response::HTTP_NOT_FOUND);
        }

        return $catalogItem;
    }

    /**
     * Toggle like for advert
     * @param Advert $advert
     * @throws AdvertException
     * @return void
     */
    public function toggleLike(Advert $advert): void
    {
        try {
            DB::transaction(function () use ($advert) {
                $advert->is_liked = !$advert->is_liked;
                $advert->liked_at = $advert->is_liked ? now() : null;
                $advert->selection->is_liked = true;

                $advert->save();
                $advert->selection->save();
            });
        } catch (\Throwable $e) {
            throw new AdvertException('Error while setting like', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
