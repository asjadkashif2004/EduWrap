<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Repositories\ActivityRepository;
use App\Repositories\EnrollmentRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Collection;

class EnrollmentService
{
    public function __construct(
        private readonly EnrollmentRepository $enrollmentRepository,
        private readonly ActivityRepository $activityRepository,
        private readonly NotificationService $notificationService,
        private readonly WebhookService $webhookService,
    ) {
    }

    public function enroll(User $user, Course $course): Enrollment
    {
        $enrollment = $this->enrollmentRepository->createIfNotExists($user->id, $course->id);

        if ($enrollment->wasRecentlyCreated) {
            $course->increment('enrollment_count');
        }

        $payload = [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
        ];

        $this->webhookService->dispatchInternalEvent('course.enrolled', $payload);

        $this->notificationService->pushToUser(
            user: $user,
            title: 'Course Enrolled Successfully',
            message: "You are now enrolled in {$course->title}.",
            eventType: 'course.enrolled',
            data: $payload
        );

        $this->activityRepository->log($user->id, $course->id, 'course_enrolled');

        return $enrollment->load('course.lessons');
    }

    public function updateProgress(User $user, Enrollment $enrollment, array $completedLessonIds): Enrollment
    {
        $courseLessonIds = $enrollment->course->lessons()->pluck('id')->all();
        $validCompletedIds = array_values(array_intersect($courseLessonIds, array_unique(array_map('intval', $completedLessonIds))));
        $completedLessons = count($validCompletedIds);
        $totalLessons = max(1, count($courseLessonIds));
        $progress = (int) floor(($completedLessons / $totalLessons) * 100);
        $updated = $this->enrollmentRepository->updateProgress($enrollment, $completedLessons, $progress, $validCompletedIds);

        if ($updated->progress_percentage >= 100) {
            $updated->course->increment('completion_count');

            $payload = [
                'user_id' => $user->id,
                'course_id' => $updated->course_id,
                'enrollment_id' => $updated->id,
            ];

            $this->webhookService->dispatchInternalEvent('course.completed', $payload);
            $this->notificationService->pushToUser(
                user: $user,
                title: 'Course Completed',
                message: "Great work! You completed {$updated->course->title}.",
                eventType: 'course.completed',
                data: $payload
            );
        }

        $this->activityRepository->log(
            userId: $user->id,
            courseId: $updated->course_id,
            activityType: 'progress_updated'
        );

        return $updated->load('course.lessons');
    }

    public function listUserEnrollments(User $user): Collection
    {
        return $this->enrollmentRepository->listByUser($user->id);
    }

    public function certificateData(User $user, Enrollment $enrollment): array
    {
        if ($enrollment->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'enrollment' => ['You are not authorized to access this enrollment certificate.'],
            ]);
        }

        if ((int) $enrollment->progress_percentage < 100) {
            throw ValidationException::withMessages([
                'progress' => ['Certificate is available only after 100% course completion.'],
            ]);
        }

        $enrollment = $enrollment->loadMissing('course');

        return [
            'enrollment_id' => $enrollment->id,
            'is_completed' => true,
            'progress_percentage' => (int) $enrollment->progress_percentage,
            'user_name' => $user->name,
            'course_title' => (string) $enrollment->course->title,
            'completion_date' => optional($enrollment->completed_at)->toDateString() ?? now()->toDateString(),
            'generated_at' => optional($enrollment->certificate_generated_at)?->toISOString(),
        ];
    }

    public function markCertificateGenerated(User $user, Enrollment $enrollment): Enrollment
    {
        // Reuse completion/ownership validation to keep one source of truth.
        $this->certificateData($user, $enrollment);

        return $this->enrollmentRepository->markCertificateGenerated($enrollment);
    }
}
