<?php

namespace Database\Factories;

use App\Enums\WithdrawalStatus;
use App\Models\Partner;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawalFactory extends Factory
{
    protected $model = Withdrawal::class;

    public function definition(): array
    {
        return [
            'partner_id'   => Partner::factory(),
            'amount'       => fake()->randomElement([100_000, 250_000, 500_000, 1_000_000]),
            'status'       => WithdrawalStatus::Pending,
            'bank_account' => fake()->numerify('##############'),
            'bank_name'    => fake()->randomElement(['BCA', 'BRI', 'BNI', 'Mandiri']),
            'bank_holder'  => fake()->name(),
        ];
    }

    public function processed(): static
    {
        return $this->state([
            'status'       => WithdrawalStatus::Processed,
            'processed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status'     => WithdrawalStatus::Rejected,
            'admin_note' => fake()->sentence(),
        ]);
    }
}
