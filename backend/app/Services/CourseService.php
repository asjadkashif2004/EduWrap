<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Repositories\CourseRepository;
use Illuminate\Database\Eloquent\Collection;

class CourseService
{
    public function __construct(
        private readonly CourseRepository $courseRepository,
    ) {
    }

    public function listCourses(): Collection
    {
        return $this->courseRepository->allPublished();
    }

    /**
     * Public catalog: hide documentation, YouTube, and per-lesson media URLs (enrollment-gated).
     *
     * @return list<array<string, mixed>>
     */
    public function listCatalogPayload(): array
    {
        return $this->courseRepository->allPublished()
            ->map(fn (Course $course) => $this->stripProtectedLearningUrls($course->toArray()))
            ->values()
            ->all();
    }

    /**
     * Course detail JSON: full learning URLs only when the user is enrolled.
     *
     * @return array<string, mixed>
     */
    public function courseDetailPayload(Course $course, ?Enrollment $myEnrollment, bool $isWishlisted): array
    {
        $payload = $course->toArray();

        if ($myEnrollment === null) {
            $payload = $this->stripProtectedLearningUrls($payload);
        }

        $payload['my_enrollment'] = $myEnrollment;
        $payload['is_wishlisted'] = $isWishlisted;

        return $payload;
    }

    /**
     * Remove URLs that should only be available after enrollment.
     *
     * @param  array<string, mixed>  $courseArray
     * @return array<string, mixed>
     */
    public function stripProtectedLearningUrls(array $courseArray): array
    {
        unset(
            $courseArray['documentation_url'],
            $courseArray['geeksforgeeks_url'],
            $courseArray['w3schools_url'],
            $courseArray['youtube_url'],
        );

        if (!empty($courseArray['lessons']) && is_array($courseArray['lessons'])) {
            foreach ($courseArray['lessons'] as $i => $lesson) {
                if (!is_array($lesson)) {
                    continue;
                }
                unset(
                    $lesson['video_url'],
                    $lesson['notes_url'],
                    $lesson['supplementary_video_url'],
                    $lesson['geeksforgeeks_url'],
                    $lesson['w3schools_url'],
                );
                $courseArray['lessons'][$i] = $lesson;
            }
        }

        return $courseArray;
    }

    public function getCourse(int $courseId)
    {
        return $this->courseRepository->findPublishedById($courseId);
    }
}
