<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Repositories\WishlistRepository;

class WishlistService
{
    public function __construct(
        private readonly WishlistRepository $wishlistRepository,
        private readonly CourseService $courseService,
        private readonly NotificationService $notificationService,
    ) {
    }

    public function getWishlist(User $user): array
    {
        $rows = $this->wishlistRepository->listWithCourses($user->id);
        $enrolledIds = $user->enrollments()->pluck('course_id')->flip();

        return [
            'items' => $rows->map(function ($row) use ($enrolledIds) {
                $courseArray = $row->course->toArray();
                if (!isset($enrolledIds[$row->course_id])) {
                    $courseArray = $this->courseService->stripProtectedLearningUrls($courseArray);
                }

                return [
                    'id' => $row->id,
                    'course_id' => $row->course_id,
                    'course' => $courseArray,
                ];
            })->values()->all(),
        ];
    }

    public function addCourse(User $user, Course $course): array
    {
        $item = $this->wishlistRepository->add($user->id, $course->id);

        if ($item->wasRecentlyCreated) {
            $this->notificationService->pushToUser(
                user: $user,
                title: 'Wishlist updated',
                message: "Saved “{$course->title}” to your wishlist.",
                eventType: 'wishlist.added',
                data: ['course_id' => $course->id],
            );
        }

        return $this->getWishlist($user);
    }

    public function removeCourse(User $user, int $courseId): array
    {
        $course = Course::query()->find($courseId);
        $deleted = $this->wishlistRepository->remove($user->id, $courseId);

        if ($deleted > 0 && $course) {
            $this->notificationService->pushToUser(
                user: $user,
                title: 'Wishlist updated',
                message: "Removed “{$course->title}” from your wishlist.",
                eventType: 'wishlist.removed',
                data: ['course_id' => $courseId],
            );
        }

        return $this->getWishlist($user);
    }
}
