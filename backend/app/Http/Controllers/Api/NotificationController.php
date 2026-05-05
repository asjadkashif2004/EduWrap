<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {
    }

    public function index(Request $request)
    {
        return response()->json($this->notificationService->history($request->user()));
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'unread_count' => $this->notificationService->unreadCount($request->user()),
        ]);
    }

    public function markAllRead(Request $request)
    {
        $this->notificationService->markAllRead($request->user());

        return response()->noContent();
    }

    public function destroyAll(Request $request)
    {
        $this->notificationService->clearAll($request->user());

        return response()->noContent();
    }
}
