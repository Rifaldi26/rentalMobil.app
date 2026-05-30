<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Withdrawal;

class WithdrawalPolicy
{
    /** Partner pemilik atau admin bisa lihat. */
    public function view(User $user, Withdrawal $withdrawal): bool
    {
        return $user->isAdmin() || $withdrawal->user_id === $user->id;
    }

    /** Hanya partner terverifikasi dengan saldo cukup. */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Hanya admin yang bisa approve/reject. */
    public function process(User $user): bool
    {
        return $user->isAdmin();
    }
}
