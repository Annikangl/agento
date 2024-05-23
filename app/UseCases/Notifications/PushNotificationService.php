<?php

namespace App\UseCases\Notifications;

use Berkayk\OneSignal\OneSignalFacade as OneSignal;

class PushNotificationService
{
    /**
     * Send push to user
     * @param array $userIds
     * @param string $title
     * @param string $message
     * @param array $data
     * @return void
     */
    public function sendToUser(array $userIds, string $title, string $message, array $data = []): void
    {
        OneSignal::sendNotificationToExternalUser(
            message: $message,
            userId: $userIds,
            headings: $title,
            data: empty($data) ? null : $data,
        );
    }

    /**
     * Send push to all users
     * @param string $title
     * @param string $message
     * @return void
     */
    public function sendToAll(string $title, string $message): void
    {
        OneSignal::sendNotificationToAll(
            message: $message,
            headings: $title,
        );
    }
}
