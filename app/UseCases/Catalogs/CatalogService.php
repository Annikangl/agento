<?php

namespace App\UseCases\Catalogs;

use App\Models\Catalogs\Property\CatalogProperty;
use App\Models\Selection\Selection;
use Illuminate\Pagination\LengthAwarePaginator;

class CatalogService
{
    /**
     * Get, filtered and paginate active catalog items from other catalog models by selection filters order by created_at
     * @param Selection $selection
     * @return LengthAwarePaginator
     */
    public function getPaginationCatalog(Selection $selection): LengthAwarePaginator
    {
        $catalogQuery =  CatalogProperty::query()
            ->has('images')
            ->with('images')
            ->active()
            ->byDealType($selection->deal_type)
            ->byPropertyType($selection->property_type)
            ->byCompletion($selection->completion)
            ->byBedroomsCount($selection->beds)
            ->byAddress($selection->location_type, $selection->location)
            ->byBudgetRange($selection->budget_from, $selection->budget_to)
            ->bySize($selection->size_units, $selection->size_from, $selection->size_to);

        if ($priceSort = request()->input('sort.price')) {
            $catalogQuery->sortByPrice($priceSort);
        }

        if ($newestSort = request()->input('sort.newest')) {
            $catalogQuery->sortByNewest($newestSort);
        }

        return $catalogQuery->paginate(25);
    }
}
