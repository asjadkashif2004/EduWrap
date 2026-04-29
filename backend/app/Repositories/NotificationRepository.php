<?php

namespace App\Repositories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository
{
    public function create(
        ?int $userId,
        string $title,
        string $message,
        ?string $eventType = null,
        array $data = []
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'event_type' => $eventType,
            'data' => $data,
        ]);
    }

    public function listByUser(int $userId): Collection
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }
}
