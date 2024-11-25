<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Farm;
use Illuminate\Auth\Access\Response;

class FarmPolicy
{
    public function create(User $user): bool
    {
        return $user->farmer && $user->farmer->IsApproved;
    }

    public function update(User $user, Farm $farm): bool
    {
        return $farm->farmer_id === $user->farmer->id;
    }

    public function delete(User $user, Farm $farm): bool
    {
        return $farm->farmer_id === $user->farmer->id;
    }

}
