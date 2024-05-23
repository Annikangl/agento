<?php

namespace App\Http\Controllers\Api\v1\Selection;

use App\Exceptions\Api\System\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Selection\CreateAdvertsRequest;
use App\Http\Resources\Selection\Advert\PaginaterAdvertCollection;
use App\Http\Resources\Selection\SelectionResource;
use App\Models\Selection\Advert;
use App\Models\Selection\Selection;
use App\UseCases\Notifications\PushNotificationService;
use App\UseCases\Selection\AdvertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AdvertController extends Controller
{
    public function __construct(private readonly AdvertService $advertService, private readonly PushNotificationService $pushService)
    {
    }

    /**
     * Get adverts list by selection
     * @param Selection $selection
     * @return JsonResponse
     */
    public function getBySelection(Selection $selection): JsonResponse
    {
//        $this->authorize('view', $selection);

        $adverts = $selection->adverts()
            ->with(['catalogable', 'catalogable.images'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => true,
            'adverts_count' => $adverts->total(),
            'liked_adverts' => $selection->adverts()->where('is_liked', true)->count(),
            'adverts' => new PaginaterAdvertCollection($adverts)
        ])
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function createAdverts(CreateAdvertsRequest $request)
    {
        $selection = Selection::query()->find($request->validated('selection_id'));

        $this->authorize('create', [Advert::class, $selection]);

        $this->advertService->createBySelection(
            collect($request->validated('added_catalog_items')),
            $selection,
        );

        return response()->json(['status' => true, 'selection' => new SelectionResource($selection->load('adverts'))])
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @throws \Throwable
     */
    public function toggleLike(Advert $advert)
    {
        $this->advertService->toggleLike($advert);

        if ($advert->is_liked) {
            $this->pushService->sendToUser(
                userIds: [(string)$advert->selection->user->id],
                title: 'Advert liked',
                message: "A new like on your selection: {$advert->selection->title}",
                data: ['selection_id' => $advert->selection->id]
            );
        }

        return response()->json(['status' => true, 'message' => 'Like set'])
            ->setStatusCode(Response::HTTP_OK);
    }
}
