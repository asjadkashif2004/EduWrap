<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class RecommendationService
{
    public function recommendForUser(User $user): Collection
    {
        $enrolledCourseIds = $user->enrollments()->pluck('course_id');
        $categories = Course::query()->whereIn('id', $enrolledCourseIds)->pluck('category')->filter();

        $categoryBased = Course::query()
            ->when($categories->isNotEmpty(), fn ($query) => $query->whereIn('category', $categories))
            ->whereNotIn('id', $enrolledCourseIds)
            ->where('is_published', true)
            ->orderByDesc('enrollment_count')
            ->limit(5)
            ->get();

        if ($categoryBased->isNotEmpty()) {
            return $categoryBased;
        }

        return Course::query()
            ->whereNotIn('id', $enrolledCourseIds)
            ->where('is_published', true)
            ->orderByDesc('enrollment_count')
            ->limit(5)
            ->get();
    }
}
