<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy untuk Vehicle.
 *
 * Setelah refactor domain:
 * - Semua kendaraan adalah milik usaha (tidak ada owner per kendaraan)
 * - Hanya Admin yang bisa create/update/delete kendaraan
 * - Pelanggan hanya bisa view
 */
class VehiclePolicy
{
    use HandlesAuthorization;

    /** Siapa saja bisa melihat kendaraan yang sudah published. */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /** Siapa saja bisa melihat detail kendaraan. */
    public function view(?User $user, Vehicle $vehicle): bool
    {
        // Kendaraan belum diverifikasi hanya bisa dilihat admin
        if (! $vehicle->is_verified || ! $vehicle->is_active) {
            return $user?->isAdmin() ?? false;
        }
        return true;
    }

    /** Hanya admin yang bisa membuat kendaraan baru. */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Hanya admin yang bisa mengubah kendaraan. */
    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->isAdmin();
    }

    /** Hanya admin yang bisa menghapus kendaraan. */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->isAdmin();
    }

    /** Hanya admin yang bisa mengelola jadwal/blokir tanggal. */
    public function manageSchedule(User $user, Vehicle $vehicle): bool
    {
        return $user->isAdmin();
    }
}
