<?php

namespace App\Repositories;

use App\Models\WishlistItem;
use Illuminate\Database\Eloquent\Collection;

class WishlistRepository
{
    public function listWithCourses(int $userId): Collection
    {
        return WishlistItem::query()
            ->where('user_id', $userId)
            ->with(['course' => fn ($q) => $q->with('lessons')])
            ->orderByDesc('created_at')
            ->get();
    }

    public function add(int $userId, int $courseId): WishlistItem
    {
        return WishlistItem::firstOrCreate(
            ['user_id' => $userId, 'course_id' => $courseId]
        );
    }

    public function remove(int $userId, int $courseId): int
    {
        return WishlistItem::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->delete();
    }
}
