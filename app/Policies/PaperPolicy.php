<?php

namespace App\Policies;

use App\Models\Paper;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaperPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Paper $paper): bool
    {
        return $user->id === $paper->institute_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Paper $paper): bool
    {
        return $user->id === $paper->institute_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Paper $paper): bool
    {
        return $user->id === $paper->institute_id;
    }

    /**
     * Custom policy for the preview action.
     */
    public function preview(User $user, Paper $paper): bool
    {
        return $user->id === $paper->institute_id;
    }
    
    /**
     * Custom policy for the autoFillBlueprint action.
     */
    public function autoFillBlueprint(User $user, Paper $paper): bool
    {
        return $user->id === $paper->institute_id;
    }
}