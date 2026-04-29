<?php

namespace App\Repositories;

use App\Models\UserActivity;
use Illuminate\Support\Facades\DB;

class ActivityRepository
{
    public function log(int $userId, ?int $courseId, string $activityType, int $durationSeconds = 0, array $meta = []): UserActivity
    {
        return UserActivity::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'activity_type' => $activityType,
            'duration_seconds' => $durationSeconds,
            'meta' => $meta,
        ]);
    }

    public function insights(): array
    {
        return [
            'total_time_spent_seconds' => (int) UserActivity::query()->sum('duration_seconds'),
            'course_popularity' => DB::table('courses')
                ->select(['id', 'title', 'enrollment_count', 'completion_count'])
                ->orderByDesc('enrollment_count')
                ->limit(10)
                ->get(),
            'completion_rates' => DB::table('courses as c')
                ->leftJoin('enrollments as e', 'e.course_id', '=', 'c.id')
                ->selectRaw(
                    'c.id, c.title, COUNT(e.id) as total_enrollments, '.
                    'SUM(CASE WHEN e.progress_percentage >= 100 THEN 1 ELSE 0 END) as total_completed'
                )
                ->groupBy('c.id', 'c.title')
                ->get()
                ->map(function ($row) {
                    $rate = $row->total_enrollments > 0
                        ? round(($row->total_completed / $row->total_enrollments) * 100, 2)
                        : 0;

                    return [
                        'course_id' => $row->id,
                        'course_title' => $row->title,
                        'completion_rate' => $rate,
                    ];
                }),
        ];
    }
}
