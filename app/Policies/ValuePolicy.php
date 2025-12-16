<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Value;
use Illuminate\Auth\Access\HandlesAuthorization;

class ValuePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the value can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('list values');
    }

    /**
     * Determine whether the value can view the model.
     */
    public function view(User $user, Value $model): bool
    {
        return $user->hasPermissionTo('view values');
    }

    /**
     * Determine whether the value can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create values');
    }

    /**
     * Determine whether the value can update the model.
     */
    public function update(User $user, Value $model): bool
    {
        return $user->hasPermissionTo('update values');
    }

    /**
     * Determine whether the value can delete the model.
     */
    public function delete(User $user, Value $model): bool
    {
        return $user->hasPermissionTo('delete values');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete values');
    }

    /**
     * Determine whether the value can restore the model.
     */
    public function restore(User $user, Value $model): bool
    {
        return false;
    }

    /**
     * Determine whether the value can permanently delete the model.
     */
    public function forceDelete(User $user, Value $model): bool
    {
        return false;
    }
}
