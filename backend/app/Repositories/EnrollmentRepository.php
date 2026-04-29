<?php

namespace App\Repositories;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Collection;

class EnrollmentRepository
{
    public function createIfNotExists(int $userId, int $courseId): Enrollment
    {
        return Enrollment::firstOrCreate(
            ['user_id' => $userId, 'course_id' => $courseId],
            ['completed_lessons' => 0, 'completed_lesson_ids' => [], 'progress_percentage' => 0]
        );
    }

    public function listByUser(int $userId): Collection
    {
        return Enrollment::query()
            ->where('user_id', $userId)
            ->with('course.lessons')
            ->latest()
            ->get();
    }

    public function updateProgress(
        Enrollment $enrollment,
        int $completedLessons,
        int $progressPercentage,
        array $completedLessonIds
    ): Enrollment {
        $isComplete = $progressPercentage >= 100;

        $enrollment->update([
            'completed_lessons' => $completedLessons,
            'completed_lesson_ids' => array_values($completedLessonIds),
            'progress_percentage' => min(100, $progressPercentage),
            'completed_at' => $isComplete ? now() : null,
        ]);

        return $enrollment->refresh();
    }

    public function markCertificateGenerated(Enrollment $enrollment): Enrollment
    {
        $enrollment->update([
            'certificate_generated_at' => now(),
        ]);

        return $enrollment->refresh();
    }
}
