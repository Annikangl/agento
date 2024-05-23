<?php

namespace App\Http\Controllers\Api\v1\Selection;

use App\DTOs\Selection\SelectionCreateDto;
use App\Exceptions\Api\Selection\CreateSelectionException;
use App\Exceptions\Api\Selection\CreateSelectionUniquidException;
use App\Exceptions\Api\Selection\DeleteSelectionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Selection\CreateSelectionRequest;
use App\Http\Resources\Selection\SelectionCollection;
use App\Http\Resources\Selection\SelectionResource;
use App\Models\Selection\Selection;
use App\UseCases\Selection\SelectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\Exceptions\MissingCastTypeException;

class SelectionController extends Controller
{
    public function __construct(private readonly SelectionService $selectionService)
    {
    }

    /**
     * Get selection with advert by auth user.
     * If search query is provided, it will filter by title
     * @param Request $request
     * @return JsonResponse
     */
    public function getByUser(Request $request): JsonResponse
    {
        $selections = $this->selectionService->getByUserId(
            Auth::id(),
            $request->get('search')
        );

        return response()->json([
            'status' => true,
            'selections' => SelectionCollection::make($selections)
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param CreateSelectionRequest $request
     * @return JsonResponse
     * @throws CastTargetException|MissingCastTypeException|CreateSelectionException
     */
    public function store(CreateSelectionRequest $request): JsonResponse
    {
        $selectionDto = SelectionCreateDto::fromArray($request->validated());

        $selection = $this->selectionService->create(
            $selectionDto,
            Auth::id()
        );

        return response()->json([
            'status' => true,
            'selection' => new SelectionResource($selection)
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @throws DeleteSelectionException
     */
    public function destroy(Selection $selection)
    {
        $this->authorize('delete', $selection);

        $this->selectionService->delete($selection);

        return response()->json([
            'status' => true,
            'message' => 'Selection deleted'
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @throws CreateSelectionUniquidException
     */
    public function generateLink(Selection $selection)
    {
        $this->selectionService->createUniqueId($selection);

        return response()->json([
            'status' => true,
            'link' => route('selection.show', $selection->uniqueid)
        ])->setStatusCode(Response::HTTP_OK);
    }
}
