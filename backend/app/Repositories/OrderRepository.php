<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    public function createOrder(int $userId, float $totalAmount, string $currency = 'USD'): Order
    {
        return Order::create([
            'user_id' => $userId,
            'status' => 'confirmed',
            'total_amount' => $totalAmount,
            'currency' => $currency,
        ]);
    }
}
