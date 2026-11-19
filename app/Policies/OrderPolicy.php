<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Detail pesanan hanya boleh dilihat oleh:
     * - Admin
     * - Seller pemilik kantin yang bersangkutan
     * - Customer yang membuat pesanan tersebut
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isSeller() && $user->canteen?->id === $order->canteen_id) {
            return true;
        }

        if ($user->isCustomer() && $user->id === $order->customer_id) {
            return true;
        }

        return false;
    }

    /**
     * Seller hanya boleh mengubah status pesanan yang masuk ke kantinnya.
     */
    public function updateStatus(User $user, Order $order): bool
    {
        return $user->isSeller() && $user->canteen?->id === $order->canteen_id;
    }
}
