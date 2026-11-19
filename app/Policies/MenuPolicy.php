<?php

namespace App\Policies;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\User;

class MenuPolicy
{
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

    public function view(?User $user, Menu $menu): bool
    {
        return true;
    }

    /**
     * Seller hanya boleh menambahkan menu ke kantinnya sendiri.
     */
    public function create(User $user, Canteen $canteen): bool
    {
        return $user->isSeller() && $user->canteen?->id === $canteen->id;
    }

    /**
     * Seller hanya boleh mengedit menu kantinnya sendiri.
     */
    public function update(User $user, Menu $menu): bool
    {
        return $user->isSeller() && $user->canteen?->id === $menu->canteen_id;
    }

    /**
     * Seller hanya boleh menghapus menu kantinnya sendiri.
     */
    public function delete(User $user, Menu $menu): bool
    {
        return $user->isSeller() && $user->canteen?->id === $menu->canteen_id;
    }
}
