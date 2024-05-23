<?php

namespace App\Http\Controllers\Api\v1\Ticket;

use App\Exceptions\Api\Auth\Ticket\CreateTicketException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use App\Http\Resources\Ticket\TicketResource;
use App\UseCases\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TicketController extends Controller
{
    public function __construct(private readonly TicketService $ticketService)
    {
    }

    /**
     * @param TicketRequest $request
     * @return JsonResponse
     * @throws CreateTicketException
     */
    public function store(TicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->create(
            user: $request->user('sanctum'),
            title: $request->validated('title'),
            content: $request->validated('content'),
            agencyRegistrationData: collect($request->validated('agency_data'))
        );

        return response()->json(['status' => true, 'ticket' => new TicketResource($ticket)])
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
