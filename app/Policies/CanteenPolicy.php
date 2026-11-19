<?php

namespace App\Policies;

use App\Models\Canteen;
use App\Models\User;

class CanteenPolicy
{
    /**
     * Admin memiliki akses universal ke seluruh aksi kantin.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Canteen $canteen): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Seller hanya boleh mengelola kantin yang menjadi kepemilikannya.
     */
    public function update(User $user, Canteen $canteen): bool
    {
        return $user->isSeller() && $user->canteen?->id === $canteen->id;
    }

    public function delete(User $user, Canteen $canteen): bool
    {
        return $user->isAdmin();
    }
}
