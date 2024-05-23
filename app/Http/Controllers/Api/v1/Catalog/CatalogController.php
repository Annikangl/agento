<?php

namespace App\Http\Controllers\Api\v1\Catalog;

use App\Exceptions\Api\Selection\GetCatalogBySelectionException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Catalogs\CatalogCollection;
use App\Models\Selection\Selection;
use App\UseCases\Catalogs\CatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CatalogController extends Controller
{
    public function __construct(private readonly CatalogService $catalogService)
    {
    }

    /**
     * @param Selection $selection
     * @return JsonResponse
     * @throws GetCatalogBySelectionException
     */
    public function getBySelection(Selection $selection): JsonResponse
    {
        try {
            $catalog = $this->catalogService->getPaginationCatalog($selection);
        } catch (\Throwable $exception) {
            throw new GetCatalogBySelectionException($exception->getMessage());
        }

        return response()->json([
            'status' => true,
            'catalog' => new CatalogCollection($catalog)
        ])->setStatusCode(Response::HTTP_OK);
    }
}
