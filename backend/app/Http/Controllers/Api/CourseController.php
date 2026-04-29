<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(
        private readonly CourseService $courseService,
    ) {
    }

    public function index()
    {
        return response()->json($this->courseService->listCourses());
    }

    public function show(Request $request, int $courseId)
    {
        $course = $this->courseService->getCourse($courseId);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $course->loadMissing('lessons');

        $myEnrollment = Enrollment::query()
            ->where('user_id', $request->user()->id)
            ->where('course_id', $courseId)
            ->first();

        $payload = $course->toArray();
        $payload['my_enrollment'] = $myEnrollment;

        return response()->json($payload);
    }
}
