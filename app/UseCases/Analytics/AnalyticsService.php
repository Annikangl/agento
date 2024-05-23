<?php

namespace App\UseCases\Analytics;

use App\Models\Analytics\AnalyticsAppVisit;
use App\Models\User\User;

class AnalyticsService
{
    public function trackUserVisit(User $user, string $deviceId, string $deviceName): void
    {
        AnalyticsAppVisit::query()->create([
            'user_id' => $user->id,
            'device_name' => $deviceName,
            'device_id' => $deviceId
        ]);
    }
}
