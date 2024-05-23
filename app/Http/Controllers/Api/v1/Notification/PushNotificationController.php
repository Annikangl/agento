<?php

namespace App\Http\Controllers\Api\v1\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notification\PaginatedPushNotificationCollection;
use App\Models\Notification\PushNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function getHistoryByUser(Request $request): JsonResponse
    {
        $generalPush = PushNotification::query()
            ->select(['id', 'title', 'content', 'created_at'])
            ->whereNull('user_id');

        $userPush = PushNotification::query()
                ->select(['id', 'title', 'content', 'created_at'])
                ->where('user_id', $request->user()->id);

        $pushs = $generalPush->union($userPush)
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => true,
            'notifications' => new PaginatedPushNotificationCollection($pushs),
        ]);
    }
}
