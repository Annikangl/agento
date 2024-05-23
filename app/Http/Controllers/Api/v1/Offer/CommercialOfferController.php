<?php

namespace App\Http\Controllers\Api\v1\Offer;

use App\DTOs\Offer\CommercialOfferCreateDto;
use App\Exceptions\Api\Services\PDFCreateException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Offer\CreateCommercialOfferRequest;
use App\Http\Resources\Offer\CommercialOfferResource;
use App\Http\Resources\Offer\CommercialOfferStatusResource;
use App\Http\Resources\Offer\PaginateOffersCollection;
use App\Jobs\ProcessPDF;
use App\Models\Offer\CommercialOffer;
use App\UseCases\CommercialOfferService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\Exceptions\MissingCastTypeException;

class CommercialOfferController extends Controller
{
    /**
     * @param CommercialOfferService $offerService
     */
    public function __construct(private readonly CommercialOfferService $offerService)
    {
    }

    /**
     * Returns created offers by user
     * @return JsonResponse
     */
    public function byUser(): JsonResponse
    {
        $offers = auth()->user()
            ->commercialOffers()
            ->active()
            ->latest()
            ->paginate(15);

        return response()->json(['status' => true, 'commercial_offers' => new PaginateOffersCollection($offers)])
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Check created offer status
     * @param CommercialOffer $commercialOffer
     * @return JsonResponse
     */
    public function checkStatus(CommercialOffer $commercialOffer): JsonResponse
    {
        return response()->json([
            'status' => true,
            'commercial_offer' => new CommercialOfferStatusResource($commercialOffer)
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Return offer
     * @param CommercialOffer $commercialOffer
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(CommercialOffer $commercialOffer)
    {
        return response()->json(['status' => true, 'commercial_offer' => new CommercialOfferResource($commercialOffer)])
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Create a new offer and start pdf process
     * @param CreateCommercialOfferRequest $request
     * @return JsonResponse
     * @throws PDFCreateException|CastTargetException|MissingCastTypeException
     */
    public function store(CreateCommercialOfferRequest $request): JsonResponse
    {
        $offerDto = CommercialOfferCreateDto::fromArray($request->validated());

        $offer = $this->offerService->create(
            userId: auth()->id(),
            dto: $offerDto,
        );

        $options = collect($request->validated())->except('source_link');

        ProcessPDF::dispatch($offer, $options);

        return response()->json(['status' => true, 'commercial_offer' => new CommercialOfferResource($offer)])
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Delete offer and associated file
     * @param CommercialOffer $commercialOffer
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(CommercialOffer $commercialOffer)
    {
        $this->authorize('delete', $commercialOffer);

        $this->offerService->delete($commercialOffer);

        return response()->json(['status' => true, 'message' => "Offer {$commercialOffer->id} deleted"])
            ->setStatusCode(Response::HTTP_OK);
    }
}
