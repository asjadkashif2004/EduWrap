<?php

namespace App\Repositories;

use App\Models\Cart;

class CartRepository
{
    public function getOrCreateByUserId(int $userId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $userId]);
    }

    public function loadWithItems(Cart $cart): Cart
    {
        return $cart->load('items.course');
    }
}
