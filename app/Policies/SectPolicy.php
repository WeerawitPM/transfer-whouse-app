<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Sect;
use Illuminate\Auth\Access\HandlesAuthorization;

class SectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_sect');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sect $sect): bool
    {
        return $user->can('view_sect');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_sect');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sect $sect): bool
    {
        return $user->can('update_sect');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sect $sect): bool
    {
        return $user->can('delete_sect');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_sect');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Sect $sect): bool
    {
        return $user->can('force_delete_sect');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_sect');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Sect $sect): bool
    {
        return $user->can('restore_sect');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_sect');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Sect $sect): bool
    {
        return $user->can('replicate_sect');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_sect');
    }
}
