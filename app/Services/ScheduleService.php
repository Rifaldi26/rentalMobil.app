<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Vehicle;
use App\Models\VehicleAvailability;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ScheduleService
{
    /**
     * Ambil data ketersediaan untuk ditampilkan di kalender UI.
     *
     * @return array{
     *   blocked_dates: array<string>,
     *   booked_ranges: array<array{start: string, end: string, status: string, booking_code: string}>
     * }
     */
    public function getAvailabilityData(Vehicle $vehicle, ?int $year = null, ?int $month = null): array
    {
        $year  = $year  ?? now()->year;
        $month = $month ?? now()->month;

        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $blockedDates = $vehicle->availabilities()
            ->whereBetween('blocked_date', [$startOfMonth, $endOfMonth])
            ->get(['blocked_date', 'reason'])
            ->map(fn ($a) => [
                'date'   => $a->blocked_date->format('Y-m-d'),
                'reason' => $a->reason,
            ]);

        $bookedRanges = $vehicle->bookings()
            ->whereIn('status', array_map(
                fn (BookingStatus $s) => $s->value,
                BookingStatus::conflictStatuses()
            ))
            ->where('start_date', '<=', $endOfMonth)
            ->where('end_date', '>=', $startOfMonth)
            ->with('user:id,name')
            ->get(['id', 'booking_code', 'user_id', 'start_date', 'end_date', 'status'])
            ->map(fn ($b) => [
                'start'        => $b->start_date->format('Y-m-d'),
                'end'          => $b->end_date->format('Y-m-d'),
                'status'       => $b->status?->value,
                'status_label' => $b->status->label(),
                'booking_code' => $b->booking_code,
                'customer'     => $b->user->name,
            ]);

        return [
            'blocked_dates' => $blockedDates->toArray(),
            'booked_ranges' => $bookedRanges->toArray(),
            'month'         => $startOfMonth->format('Y-m'),
            'month_label'   => $startOfMonth->translatedFormat('F Y'),
        ];
    }

    /**
     * Blokir satu atau beberapa tanggal untuk satu kendaraan.
     * Tanggal yang sudah ada booking aktif tidak bisa diblokir.
     *
     * @param  array<string>  $dates  Format 'Y-m-d'
     * @return array{blocked: int, skipped: int, conflicts: array<string>}
     */
    public function blockDates(Vehicle $vehicle, array $dates, ?string $reason = null): array
    {
        $blocked   = 0;
        $conflicts = [];

        foreach ($dates as $date) {
            $dateCarbon = Carbon::parse($date);

            // Jangan blokir tanggal yang sudah ada booking aktif
            if ($this->hasActiveBookingOn($vehicle, $dateCarbon)) {
                $conflicts[] = $date;
                continue;
            }

            VehicleAvailability::firstOrCreate(
                ['vehicle_id' => $vehicle->id, 'blocked_date' => $date],
                ['reason' => $reason]
            );

            $blocked++;
        }

        return [
            'blocked'   => $blocked,
            'skipped'   => count($conflicts),
            'conflicts' => $conflicts,
        ];
    }

    /**
     * Batalkan blokir sebuah tanggal.
     * Tidak bisa membuka blokir jika ada booking aktif di tanggal tersebut.
     */
    public function unblockDate(Vehicle $vehicle, string $date): bool
    {
        return (bool) VehicleAvailability::where('vehicle_id', $vehicle->id)
            ->where('blocked_date', $date)
            ->delete();
    }

    /**
     * Blokir rentang tanggal (mis. untuk servis kendaraan).
     *
     * @return array{blocked: int, skipped: int, conflicts: array<string>}
     */
    public function blockDateRange(
        Vehicle $vehicle,
        string $startDate,
        string $endDate,
        ?string $reason = null
    ): array {
        $dates   = [];
        $current = Carbon::parse($startDate);
        $end     = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        return $this->blockDates($vehicle, $dates, $reason);
    }

    // ─── Private ─────────────────────────────────────────────────

    private function hasActiveBookingOn(Vehicle $vehicle, Carbon $date): bool
    {
        return $vehicle->bookings()
            ->whereIn('status', array_map(
                fn (BookingStatus $s) => $s->value,
                BookingStatus::conflictStatuses()
            ))
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();
    }
}
