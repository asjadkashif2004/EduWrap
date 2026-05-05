<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\NotificationRepository;

class NotificationService
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository,
        private readonly FcmService $fcmService,
    ) {
    }

    public function pushToUser(User $user, string $title, string $message, string $eventType, array $data = []): void
    {
        $this->notificationRepository->create(
            userId: $user->id,
            title: $title,
            message: $message,
            eventType: $eventType,
            data: $data
        );

        $this->fcmService->send(
            fcmToken: $user->fcm_token,
            title: $title,
            body: $message,
            data: $data
        );
    }

    public function history(User $user): Collection
    {
        return $this->notificationRepository->listByUser($user->id);
    }

    public function unreadCount(User $user): int
    {
        return $this->notificationRepository->unreadCountForUser($user->id);
    }

    public function markAllRead(User $user): void
    {
        $this->notificationRepository->markAllReadForUser($user->id);
    }

    public function clearAll(User $user): void
    {
        $this->notificationRepository->deleteAllForUser($user->id);
    }
}
