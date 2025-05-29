<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AccessToken;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccessTokenPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the accessToken can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the accessToken can view the model.
     */
    public function view(User $user, AccessToken $model): bool
    {
        return true;
    }

    /**
     * Determine whether the accessToken can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the accessToken can update the model.
     */
    public function update(User $user, AccessToken $model): bool
    {
        return true;
    }

    /**
     * Determine whether the accessToken can delete the model.
     */
    public function delete(User $user, AccessToken $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the accessToken can restore the model.
     */
    public function restore(User $user, AccessToken $model): bool
    {
        return false;
    }

    /**
     * Determine whether the accessToken can permanently delete the model.
     */
    public function forceDelete(User $user, AccessToken $model): bool
    {
        return false;
    }
}
