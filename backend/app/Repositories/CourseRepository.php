<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class CourseRepository
{
    public function allPublished(): Collection
    {
        return Course::query()
            ->where('is_published', true)
            ->with('lessons')
            ->orderByDesc('enrollment_count')
            ->get();
    }

    public function findPublishedById(int $id): ?Course
    {
        return Course::query()
            ->where('is_published', true)
            ->with('lessons')
            ->find($id);
    }
}
