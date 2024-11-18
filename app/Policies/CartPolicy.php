<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Buyer;
use App\Models\User;
use App\Models\Cart;

class CartPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $authUser, Cart $cart): bool
    {
        $buyer = Buyer::where('user_id', $authUser->id)->first();
        if (!$buyer) {
            return false; // Deny if the user is not a buyer
        }
        // Allow if the buyer ID matches the cart's buyer ID or if the user is an admin
        return $buyer->id === $cart->buyer_id || $authUser->isAdmin();
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $authUser, Cart $cart): bool
    {
        return $authUser->id === $cart->buyer_id ||$authUser->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authUser, Cart $cart): bool
    {
        return $authUser->id === $cart->buyer_id || $authUser->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cart $cart): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cart $cart): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cart $cart): bool
    {
        //
    }
}
