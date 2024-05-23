<?php

namespace App\Http\Controllers\Api\v1\Selection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Selection\SearchAddressRequest;
use App\Http\Resources\Selection\AddressResource;
use App\Models\Catalogs\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SearchAddressController extends Controller
{
    /**
     * Get locations by name
     * @param SearchAddressRequest $request
     * @return JsonResponse
     */
    public function __invoke(SearchAddressRequest $request): JsonResponse
    {
        $takes = $request->validated('take') ?? 25;

        $addreses = Address::bySearch($request->validated('name'))->take($takes)->get();

        return response()->json(['status' => true, 'locations' => AddressResource::collection($addreses)])
            ->setStatusCode(Response::HTTP_OK);
    }
}
