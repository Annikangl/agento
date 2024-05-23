<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\App\BannerResource;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class GetBannerController extends Controller
{
    /**
     * Get banner list
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'banners' => BannerResource::collection(Banner::query()->take(5)->get())
        ])
            ->setStatusCode(Response::HTTP_OK);
    }
}
