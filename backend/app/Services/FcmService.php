<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    public function send(?string $fcmToken, string $title, string $body, array $data = []): void
    {
        $serverKey = config('services.fcm.server_key');

        if (!$fcmToken || !$serverKey) {
            return;
        }

        $response = Http::withToken($serverKey)
            ->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ]);

        if ($response->failed()) {
            Log::warning('FCM push failed', ['response' => $response->json()]);
        }
    }
}
