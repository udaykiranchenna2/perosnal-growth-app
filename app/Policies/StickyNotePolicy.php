<?php

namespace App\Policies;

use App\Models\StickyNote;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class StickyNotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StickyNote $stickyNote): bool
    {
        return $user->id === $stickyNote->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StickyNote $stickyNote): bool
    {
        return $user->id === $stickyNote->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StickyNote $stickyNote): bool
    {
        return $user->id === $stickyNote->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StickyNote $stickyNote): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StickyNote $stickyNote): bool
    {
        return false;
    }
}
