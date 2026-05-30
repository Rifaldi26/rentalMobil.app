<?php
namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Enums\PemesananStatus;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 days', '+30 days');
        $end   = fake()->dateTimeBetween($start, '+45 days');
        $days  = max(1, (int) date_diff($start, $end)->days);
        $vehicle = Vehicle::factory()->create();
        $price = $days * $vehicle->price_per_day;

        return [
            'user_id'        => User::factory(),
            'vehicle_id'     => $vehicle->id,
            'start_date'     => $start,
            'end_date'       => $end,
            'total_price'    => $price,
            'deposit'        => $vehicle->deposit,
            'service_fee'    => round($price * 0.05, 2),
            'status'         => PemesananStatus::Pending,
            'payment_status' => PaymentStatus::Pending,
        ];
    }

    public function confirmed(): static
    {
        return $this->state([
            'status'         => PemesananStatus::Dikonfirmasi,
            'payment_status' => PaymentStatus::Paid,
        ]);
    }

    public function completed(): static
    {
        return $this->state([
            'status'         => PemesananStatus::Selesai,
            'payment_status' => PaymentStatus::Paid,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state([
            'status'          => PemesananStatus::Dibatalkan,
            'rejected_reason' => fake()->sentence(),
        ]);
    }
}
