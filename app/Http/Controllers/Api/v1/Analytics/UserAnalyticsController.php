<?php

namespace App\Http\Controllers\Api\v1\Analytics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Analytics\TrackVisitAnalyticsRequest;
use App\UseCases\Analytics\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UserAnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsService $analyticsService)
    {
    }

    public function trackVisit(TrackVisitAnalyticsRequest $request): JsonResponse
    {
        $this->analyticsService->trackUserVisit(
            $request->user('sanctum'),
            $request->validated('device_id'),
            $request->validated('device_name')
        );

        return response()->json(['status' => true])
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
