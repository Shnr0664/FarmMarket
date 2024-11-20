<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
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
    public function view(User $authUser, User $user): bool
    {
        return $authUser->id === $user->id || $authUser->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $authUser): bool
    {
        return $authUser->isAdmin();;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authUser, User $user): bool
    {
        return $authUser->id === $user->id || $authUser->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $authUser, User $user): bool
    {
        return $authUser->id === $user->id || $authUser->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $authUser, User $user): bool
    {
        return $authUser->id === $user->id || $authUser->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $authUser, User $user): bool
    {
        return $authUser->id === $user->id || $authUser->isAdmin();
    }
}
