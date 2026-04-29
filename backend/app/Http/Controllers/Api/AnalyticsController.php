<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ActivityRepository;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analyticsService,
        private readonly ActivityRepository $activityRepository,
    ) {
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'activity_type' => ['required', 'string', 'max:80'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'meta' => ['nullable', 'array'],
        ]);

        $activity = $this->activityRepository->log(
            userId: $request->user()->id,
            courseId: $validated['course_id'] ?? null,
            activityType: $validated['activity_type'],
            durationSeconds: (int) ($validated['duration_seconds'] ?? 0),
            meta: $validated['meta'] ?? []
        );

        return response()->json($activity, 201);
    }

    public function insights()
    {
        return response()->json($this->analyticsService->getInsights());
    }
}
