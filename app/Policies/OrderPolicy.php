<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    public function isAdmin(User $authUser): bool
    {
        return Admin::where('user_id', $authUser->id)->exists();
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $authUser, Order $order): bool
    {
        return $authUser->isAdmin() || $authUser->id === $order->buyer->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        //
    }
}
