<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Events\BookingConfirmed;
use App\Events\BookingCompleted;
use App\Events\BookingRejected;
use App\Exceptions\BookingConflictException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\VehicleNotAvailableException;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {}

    /**
     * Buat booking baru dan inisiasi transaksi pembayaran.
     *
     * Menggunakan SELECT FOR UPDATE di dalam transaksi untuk mencegah
     * race condition saat dua pelanggan memesan kendaraan yang sama secara bersamaan.
     *
     * @return array{booking: Booking, paymentData: array}
     * @throws VehicleNotAvailableException
     * @throws BookingConflictException
     */
    public function createBooking(User $user, array $data): array
    {
        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end   = Carbon::parse($data['end_date'])->startOfDay();

        $booking = DB::transaction(function () use ($user, $data, $start, $end) {
            // Lock baris kendaraan untuk mencegah race condition
            $vehicle = Vehicle::where('id', $data['vehicle_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Validasi di dalam lock — aman dari concurrent request
            $this->validateVehicleAvailable($vehicle, $start, $end);

            $days        = $start->diffInDays($end);
            $rentalTotal = $days * $vehicle->price_per_day;
            $serviceFee  = round($rentalTotal * 0.05, 2);

            return Booking::create([
                'user_id'         => $user->id,
                'vehicle_id'      => $vehicle->id,
                'start_date'      => $start,
                'end_date'        => $end,
                'total_price'     => $rentalTotal,
                'deposit'         => $vehicle->deposit,
                'service_fee'     => $serviceFee,
                'status'          => BookingStatus::Pending,
                'payment_status'  => PaymentStatus::Pending,
                'pickup_location' => $data['pickup_location'] ?? null,
                'notes'           => $data['notes'] ?? null,
            ]);
        });

        $paymentData = $this->paymentService->createTransaction($booking);

        return compact('booking', 'paymentData');
    }

    /**
     * Pelanggan membatalkan booking — hanya bisa saat status Pending.
     *
     * @throws \App\Exceptions\UnauthorizedBookingException
     */
    public function cancelByUser(Booking $booking, User $user): Booking
    {
        if (! $booking->canBeCancelledBy($user)) {
            throw new \App\Exceptions\UnauthorizedBookingException(
                'Booking tidak dapat dibatalkan atau Anda tidak memiliki akses.'
            );
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => BookingStatus::Dibatalkan]);
            $this->paymentService->refund($booking);
        });

        return $booking->fresh();
    }

    /**
     * Admin mengkonfirmasi booking masuk.
     */
    public function confirm(Booking $booking): Booking
    {
        abort_unless(
            $booking->status->canBeConfirmed(),
            422,
            "Booking dengan status '{$booking->status->label()}' tidak dapat dikonfirmasi."
        );

        DB::transaction(fn () => $booking->update(['status' => BookingStatus::Dikonfirmasi]));

        event(new BookingConfirmed($booking));

        return $booking->fresh(['user', 'vehicle']);
    }

    /**
     * Admin menolak booking — otomatis refund ke pelanggan.
     */
    public function reject(Booking $booking, string $reason): Booking
    {
        abort_unless(
            $booking->status->canBeRejected(),
            422,
            "Booking dengan status '{$booking->status->label()}' tidak dapat ditolak."
        );

        DB::transaction(function () use ($booking, $reason) {
            $booking->update([
                'status'          => BookingStatus::Ditolak,
                'rejected_reason' => $reason,
            ]);
            $this->paymentService->refund($booking);
        });

        event(new BookingRejected($booking));

        return $booking->fresh(['user', 'vehicle']);
    }

    /**
     * Admin mengaktifkan booking — kendaraan sudah diserahkan ke pelanggan.
     */
    public function activate(Booking $booking): Booking
    {
        abort_unless(
            $booking->status->canBeActivated(),
            422,
            "Booking dengan status '{$booking->status->label()}' tidak dapat diaktifkan."
        );

        DB::transaction(fn () => $booking->update(['status' => BookingStatus::Aktif]));

        return $booking->fresh(['user', 'vehicle']);
    }

    /**
     * Admin menandai booking selesai — kendaraan sudah kembali.
     * Kredit pendapatan ke saldo usaha (User role=Admin).
     */
    public function markFinished(Booking $booking): Booking
    {
        abort_unless(
            $booking->status->canBeFinished(),
            422,
            "Booking dengan status '{$booking->status->label()}' tidak dapat diselesaikan."
        );

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => BookingStatus::Selesai]);

            // Kredit 95% dari total sewa ke saldo pemilik usaha
            // Service fee 5% adalah pendapatan platform yang tidak dikredit
            $revenue = round((float) $booking->total_price * 0.95, 2);

            $owner = User::getOwner();
            $owner->creditBalance($revenue);
        });

        event(new BookingCompleted($booking));

        return $booking->fresh(['user', 'vehicle']);
    }

    // ─── Private Validators ───────────────────────────────────────

    private function validateVehicleAvailable(Vehicle $vehicle, Carbon $start, Carbon $end): void
    {
        if (! $vehicle->is_active || ! $vehicle->is_verified) {
            throw new VehicleNotAvailableException(
                "Kendaraan {$vehicle->brand} {$vehicle->model} tidak tersedia saat ini."
            );
        }

        if (! $vehicle->isAvailableOn($start, $end)) {
            throw new BookingConflictException(
                'Kendaraan sudah dipesan pada tanggal tersebut. Silakan pilih tanggal lain.'
            );
        }

        // Validasi durasi minimum
        $days = $start->diffInDays($end);
        if ($vehicle->min_rental_days && $days < $vehicle->min_rental_days) {
            throw new \InvalidArgumentException(
                "Minimum sewa kendaraan ini adalah {$vehicle->min_rental_days} hari."
            );
        }

        if ($vehicle->max_rental_days && $days > $vehicle->max_rental_days) {
            throw new \InvalidArgumentException(
                "Maksimum sewa kendaraan ini adalah {$vehicle->max_rental_days} hari."
            );
        }
    }
}