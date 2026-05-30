<?php
namespace Database\Factories;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    public function definition(): array
    {
        return [
            'user_id'      => User::factory()->partner(),
            'company_name' => fake()->company(),
            'balance'      => fake()->randomFloat(2, 0, 5_000_000),
            'bank_account' => fake()->numerify('##############'),
            'bank_name'    => fake()->randomElement(['BCA', 'BRI', 'BNI', 'Mandiri', 'GoPay']),
            'bank_holder'  => fake()->name(),
            'is_verified'  => true,
            'verified_at'  => now(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(['is_verified' => false, 'verified_at' => null]);
    }
}
