<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /** Admin dan pemilik review bisa lihat. */
    public function view(User $user, Review $review): bool
    {
        return $user->isAdmin() || $review->user_id === $user->id;
    }

    /** Hanya admin yang bisa toggle visibility. */
    public function toggleVisibility(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Hanya admin yang bisa delete review. */
    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }
}
