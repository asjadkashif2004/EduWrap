<?php

namespace App\Services;

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

    public function getCourse(int $courseId)
    {
        return $this->courseRepository->findPublishedById($courseId);
    }
}
