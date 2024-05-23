<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\PaginatedReferralCollection;
use App\Http\Resources\User\ReferralResource;
use Illuminate\Http\Response;

class ReferralController extends Controller
{
    public function byUser()
    {
        $referrals = auth()->user()->userReferals()
            ->with('referral')
            ->paginate(15);

        return response()->json(['status' => true, 'referrals' => new PaginatedReferralCollection($referrals)])
            ->setStatusCode(Response::HTTP_OK);
    }
}
