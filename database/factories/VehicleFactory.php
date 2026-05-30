<?php
namespace Database\Factories;

use App\Enums\VehicleCategory;
use App\Models\Partner;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    private static array $brands = [
        ['brand' => 'Toyota',  'models' => ['Avanza', 'Innova', 'Fortuner', 'Rush', 'Raize']],
        ['brand' => 'Honda',   'models' => ['Brio', 'Jazz', 'HR-V', 'CR-V', 'Freed']],
        ['brand' => 'Daihatsu','models' => ['Xenia', 'Terios', 'Rocky', 'Sigra']],
        ['brand' => 'Suzuki',  'models' => ['Ertiga', 'XL7', 'Ignis', 'Baleno']],
        ['brand' => 'Mitsubishi', 'models' => ['Xpander', 'Pajero', 'Outlander']],
    ];

    private static array $cities = [
        'Jakarta Selatan','Jakarta Pusat','Jakarta Barat','Bandung','Surabaya',
        'Yogyakarta','Bali','Medan','Semarang','Makassar',
    ];

    public function definition(): array
    {
        $brandData = fake()->randomElement(self::$brands);

        return [
            'partner_id'      => Partner::factory(),
            'category'        => fake()->randomElement(VehicleCategory::cases())->value,
            'brand'           => $brandData['brand'],
            'model'           => fake()->randomElement($brandData['models']),
            'year'            => fake()->numberBetween(2018, 2024),
            'plate_number'    => strtoupper(fake()->bothify('? #### ???')),
            'price_per_day'   => fake()->randomElement([200_000, 250_000, 300_000, 350_000, 400_000, 500_000]),
            'deposit'         => fake()->randomElement([100_000, 200_000, 300_000]),
            'capacity'        => fake()->randomElement([4, 5, 7, 8]),
            'transmission'    => fake()->randomElement(['matic', 'manual']),
            'fuel_type'       => fake()->randomElement(['bensin', 'diesel']),
            'description'     => fake()->paragraph(2),
            'features'        => fake()->randomElements(['ac', 'gps', 'music', 'baby_seat', 'usb_charger', 'dashcam'], 3),
            'min_rental_days' => 1,
            'max_rental_days' => 30,
            'city'            => fake()->randomElement(self::$cities),
            'is_active'       => true,
            'is_verified'     => true,
            'verified_at'     => now(),
            'avg_rating'      => null,
            'review_count'    => 0,
        ];
    }

    public function unverified(): static
    {
        return $this->state(['is_verified' => false, 'verified_at' => null]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
