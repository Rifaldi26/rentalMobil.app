<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Partner;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PartnerVehicleTest extends TestCase
{
    use RefreshDatabase;

    private User    $partnerUser;
    private Partner $partner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->partnerUser = User::factory()->partner()->create();
        $this->partner     = Partner::factory()->create([
            'user_id'     => $this->partnerUser->id,
            'is_verified' => true,
        ]);
    }

    /** @test */
    public function partner_can_view_vehicles_list(): void
    {
        Vehicle::factory()->count(2)->create(['partner_id' => $this->partner->id]);

        $this->actingAs($this->partnerUser)
            ->get(route('partner.vehicles.index'))
            ->assertOk()
            ->assertViewIs('partner.vehicles.index');
    }

    /** @test */
    public function partner_can_create_vehicle_with_photos(): void
    {
        Storage::fake('public');

        $this->actingAs($this->partnerUser)
            ->post(route('partner.vehicles.store'), [
                'category'        => 'mobil',
                'brand'           => 'Toyota',
                'model'           => 'Avanza',
                'year'            => 2022,
                'plate_number'    => 'B 1234 TST',
                'price_per_day'   => 350_000,
                'deposit'         => 200_000,
                'capacity'        => 7,
                'transmission'    => 'matic',
                'fuel_type'       => 'bensin',
                'description'     => 'Toyota Avanza 2022, terawat, AC dingin, cocok untuk keluarga.',
                'min_rental_days' => 1,
                'max_rental_days' => 30,
                'city'            => 'Jakarta Selatan',
                'photos'          => [UploadedFile::fake()->image('car.jpg')],
            ])
            ->assertRedirect(route('partner.vehicles.index'));

        $this->assertDatabaseHas('vehicles', [
            'brand'      => 'Toyota',
            'model'      => 'Avanza',
            'partner_id' => $this->partner->id,
        ]);
    }

    /** @test */
    public function partner_cannot_edit_others_vehicle(): void
    {
        $otherPartner  = Partner::factory()->create();
        $otherVehicle  = Vehicle::factory()->create(['partner_id' => $otherPartner->id]);

        $this->actingAs($this->partnerUser)
            ->get(route('partner.vehicles.edit', $otherVehicle))
            ->assertForbidden();
    }

    /** @test */
    public function unverified_partner_cannot_add_vehicles(): void
    {
        $unverifiedUser    = User::factory()->partner()->create();
        Partner::factory()->unverified()->create(['user_id' => $unverifiedUser->id]);

        $this->actingAs($unverifiedUser)
            ->post(route('partner.vehicles.store'), ['brand' => 'Honda'])
            ->assertForbidden();
    }

    /** @test */
    public function customer_cannot_access_partner_routes(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $this->actingAs($customer)
            ->get(route('partner.vehicles.index'))
            ->assertForbidden();
    }
}
