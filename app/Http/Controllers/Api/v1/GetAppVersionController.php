<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class GetAppVersionController extends Controller
{
    public function __invoke()
    {
        return response()->json(['status' => true, 'app_version' => config('app.version')])
            ->setStatusCode(Response::HTTP_OK);
    }
}
