<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Jalankan via scheduler setiap 30 menit:
 *
 * // app/Console/Kernel.php atau bootstrap/app.php:
 * Schedule::command('bookings:process-expired')->everyThirtyMinutes();
 */
class ProcessExpiredBookings extends Command
{
    protected $signature   = 'bookings:process-expired {--dry-run : Tampilkan daftar tanpa memproses}';
    protected $description = 'Tolak otomatis booking yang belum dikonfirmasi admin dalam 4 jam setelah pembayaran.';

    public function __construct(private readonly PaymentService $paymentService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        // Booking yang sudah dibayar tapi belum dikonfirmasi lebih dari 4 jam
        $expiredBookings = Booking::where('status', BookingStatus::Pending)
            ->where('payment_status', PaymentStatus::Paid)
            ->where('created_at', '<=', now()->subHours(4))
            ->with(['user', 'vehicle', 'payment'])
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('Tidak ada booking kedaluwarsa.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$expiredBookings->count()} booking kedaluwarsa.");

        if ($this->option('dry-run')) {
            $this->table(
                ['ID', 'Booking Code', 'Pelanggan', 'Kendaraan', 'Dibuat'],
                $expiredBookings->map(fn ($b) => [
                    $b->id,
                    $b->booking_code,
                    $b->user->name,
                    "{$b->vehicle->brand} {$b->vehicle->model}",
                    $b->created_at->format('d/m/Y H:i'),
                ])->toArray()
            );
            return self::SUCCESS;
        }

        $processed = 0;
        $failed    = 0;

        foreach ($expiredBookings as $booking) {
            try {
                DB::transaction(function () use ($booking) {
                    $booking->update([
                        'status'          => BookingStatus::Ditolak,
                        'rejected_reason' => 'Booking otomatis ditolak karena admin tidak merespons dalam 4 jam.',
                    ]);

                    $this->paymentService->refund($booking);
                });

                $this->line("  Ditolak: {$booking->booking_code} ({$booking->user->name})");
                $processed++;
            } catch (\Exception $e) {
                $this->error("  Gagal: {$booking->booking_code} — {$e->getMessage()}");
                Log::error('ProcessExpiredBookings gagal', [
                    'booking_id' => $booking->id,
                    'error'      => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        $this->info("Selesai: {$processed} diproses, {$failed} gagal.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
