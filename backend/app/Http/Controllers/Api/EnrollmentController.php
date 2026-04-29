<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(
        private readonly EnrollmentService $enrollmentService,
    ) {
    }

    public function enroll(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $enrollment = $this->enrollmentService->enroll($request->user(), $course);

        return response()->json($enrollment, 201);
    }

    public function updateProgress(Request $request, int $enrollmentId)
    {
        $validated = $request->validate([
            'completed_lesson_ids' => ['required', 'array'],
            'completed_lesson_ids.*' => ['integer', 'exists:lessons,id'],
        ]);

        $enrollment = Enrollment::query()
            ->where('user_id', $request->user()->id)
            ->with('course.lessons')
            ->findOrFail($enrollmentId);

        return response()->json(
            $this->enrollmentService->updateProgress(
                $request->user(),
                $enrollment,
                $validated['completed_lesson_ids']
            )
        );
    }

    public function myEnrollments(Request $request)
    {
        return response()->json($this->enrollmentService->listUserEnrollments($request->user()));
    }

    public function certificate(Request $request, int $enrollmentId)
    {
        $enrollment = Enrollment::query()
            ->with('course')
            ->findOrFail($enrollmentId);

        return response()->json(
            $this->enrollmentService->certificateData($request->user(), $enrollment)
        );
    }

    public function generateCertificate(Request $request, int $enrollmentId)
    {
        $enrollment = Enrollment::query()
            ->with('course')
            ->findOrFail($enrollmentId);

        $updated = $this->enrollmentService->markCertificateGenerated($request->user(), $enrollment);

        return response()->json([
            'message' => 'Certificate generation recorded.',
            'generated_at' => optional($updated->certificate_generated_at)?->toISOString(),
        ]);
    }
}
