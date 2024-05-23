<?php

namespace App\Http\Resources\Notification;

use App\Http\Resources\PaginateCollection;

class PaginatedPushNotificationCollection extends PaginateCollection
{
    public $collects = PushNotificationResource::class;
}
