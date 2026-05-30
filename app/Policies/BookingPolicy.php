<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    /** Pelanggan hanya bisa melihat booking miliknya. Admin bisa lihat semua. */
    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $booking->user_id === $user->id;
    }

    /** Hanya pemilik booking yang bisa membatalkan. */
    public function cancel(User $user, Booking $booking): bool
    {
        return $booking->user_id === $user->id
            && $booking->status->canBeCancelledByUser();
    }

    /** Hanya admin yang bisa konfirmasi/tolak/selesaikan. */
    public function manage(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }

    /**
     * Bisa mengakses chat booking jika:
     * - Admin (bisa chat semua booking), atau
     * - Pelanggan pemilik booking tersebut
     */
    public function chat(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $booking->user_id === $user->id;
    }

    /** Pelanggan hanya bisa review booking miliknya yang sudah selesai. */
    public function review(User $user, Booking $booking): bool
    {
        return $booking->user_id === $user->id
            && $booking->isReviewable();
    }
}
