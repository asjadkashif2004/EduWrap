<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ActivityRepository;
use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private readonly CartRepository $cartRepository,
        private readonly OrderRepository $orderRepository,
        private readonly EnrollmentService $enrollmentService,
        private readonly NotificationService $notificationService,
        private readonly ActivityRepository $activityRepository,
        private readonly WebhookService $webhookService,
    ) {
    }

    public function checkout(User $user)
    {
        return DB::transaction(function () use ($user) {
            $cart = $this->cartRepository->loadWithItems(
                $this->cartRepository->getOrCreateByUserId($user->id)
            );

            $items = $cart->items;
            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => ['Your cart is empty. Add at least one course before checkout.'],
                ]);
            }

            $validItems = $items->filter(fn ($item) => $item->course !== null)->values();
            if ($validItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => ['No purchasable courses were found in your cart.'],
                ]);
            }

            $totalAmount = (float) $validItems->sum(fn ($item) => (float) $item->course->price);

            $order = $this->orderRepository->createOrder($user->id, $totalAmount);

            foreach ($validItems as $item) {
                $order->items()->create([
                    'course_id' => $item->course_id,
                    'price' => $item->course->price,
                ]);

                $this->enrollmentService->enroll($user, $item->course);
            }

            $cart->items()->delete();
            $order->load('items.course');

            $payload = [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'total_amount' => $order->total_amount,
            ];

            $this->webhookService->dispatchInternalEvent('order.placed', $payload);

            $this->notificationService->pushToUser(
                user: $user,
                title: 'Order Confirmed',
                message: "Your order #{$order->id} has been confirmed.",
                eventType: 'order.placed',
                data: $payload
            );

            $this->activityRepository->log($user->id, null, 'order_placed');

            return $order;
        });
    }
}
