<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Course;
use App\Models\User;
use App\Repositories\CartRepository;

class CartService
{
    public function __construct(
        private readonly CartRepository $cartRepository,
    ) {
    }

    public function addCourse(User $user, Course $course): Cart
    {
        $cart = $this->cartRepository->getOrCreateByUserId($user->id);
        $cart->items()->firstOrCreate(['course_id' => $course->id]);

        return $this->cartRepository->loadWithItems($cart);
    }

    public function removeCourse(User $user, int $courseId): Cart
    {
        $cart = $this->cartRepository->getOrCreateByUserId($user->id);
        $cart->items()->where('course_id', $courseId)->delete();

        return $this->cartRepository->loadWithItems($cart);
    }

    public function getCart(User $user): Cart
    {
        return $this->cartRepository->loadWithItems(
            $this->cartRepository->getOrCreateByUserId($user->id)
        );
    }
}
