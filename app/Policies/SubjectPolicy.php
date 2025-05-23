<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Subject;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_subject');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Subject $subject): bool
    {
        return $user->can('view_subject');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_subject');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Subject $subject): bool
    {
        if (! $user->can('update_subject')) {
            return false;
        }

        if ($user->hasAnyRoleId([User::ROLE_DIRECTIVO, User::ROLE_SOPORTE])) {
            return true;
        }

        return $subject->users->contains('id', $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subject $subject): bool
    {
        return $user->can('delete_subject');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_subject');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Subject $subject): bool
    {
        return $user->can('force_delete_subject');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_subject');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Subject $subject): bool
    {
        return $user->can('restore_subject');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_subject');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Subject $subject): bool
    {
        return $user->can('replicate_subject');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_subject');
    }
}
