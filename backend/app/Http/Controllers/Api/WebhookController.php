<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\WebhookService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        private readonly WebhookService $webhookService,
        private readonly NotificationService $notificationService,
    ) {
    }

    public function handle(Request $request)
    {
        $isValid = $this->webhookService->verifySignature($request);
        $payload = $request->all();
        $eventType = $payload['event_type'] ?? 'unknown';

        $this->webhookService->log(
            eventType: $eventType,
            source: 'external',
            payload: $payload,
            signature: $request->header('X-Webhook-Signature'),
            isValid: $isValid
        );

        if (!$isValid) {
            return response()->json(['message' => 'Invalid webhook signature'], 401);
        }

        $userId = $payload['user_id'] ?? null;
        $user = $userId ? User::find($userId) : null;

        if ($user) {
            $title = match ($eventType) {
                'course.enrolled' => 'Course Enrolled Successfully',
                'course.completed' => 'Course Completed',
                'order.placed' => 'Order Confirmed',
                default => 'New Event',
            };

            $this->notificationService->pushToUser(
                user: $user,
                title: $title,
                message: "Event processed: {$eventType}",
                eventType: $eventType,
                data: $payload
            );
        }

        return response()->json(['message' => 'Webhook processed']);
    }
}
