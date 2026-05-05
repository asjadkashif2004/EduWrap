<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\CourseService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __construct(
        private readonly RecommendationService $recommendationService,
        private readonly CourseService $courseService,
    ) {
    }

    public function index(Request $request)
    {
        $courses = $this->recommendationService->recommendForUser($request->user());

        $payload = $courses
            ->map(fn (Course $course) => $this->courseService->stripProtectedLearningUrls($course->toArray()))
            ->values()
            ->all();

        return response()->json($payload);
    }
}
