<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function search_page_renders_correctly(): void
    {
        Vehicle::factory()->count(3)->create();

        $this->get(route('cars.search'))
            ->assertOk()
            ->assertViewIs('public.cars.index');
    }

    /** @test */
    public function search_filters_by_city(): void
    {
        Vehicle::factory()->create(['city' => 'Bali', 'is_verified' => true, 'is_active' => true]);
        Vehicle::factory()->create(['city' => 'Jakarta', 'is_verified' => true, 'is_active' => true]);

        $response = $this->get(route('cars.search', ['city' => 'Bali']));

        $response->assertOk();
        $vehicles = $response->viewData('vehicles');
        $this->assertEquals(1, $vehicles->total());
    }

    /** @test */
    public function vehicle_detail_page_works(): void
    {
        $vehicle = Vehicle::factory()->create(['is_verified' => true, 'is_active' => true]);

        $this->get(route('cars.show', $vehicle))
            ->assertOk()
            ->assertViewIs('public.cars.show')
            ->assertViewHas('vehicle', $vehicle);
    }

    /** @test */
    public function unverified_vehicle_returns_404(): void
    {
        $vehicle = Vehicle::factory()->unverified()->create();

        $this->get(route('cars.show', $vehicle))
            ->assertNotFound();
    }

    /** @test */
    public function availability_check_returns_json(): void
    {
        $vehicle = Vehicle::factory()->create(['is_verified' => true, 'is_active' => true]);

        $this->getJson(route('cars.availability', $vehicle), [
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date'   => now()->addDays(8)->format('Y-m-d'),
        ])->assertOk()
          ->assertJsonStructure(['available', 'days', 'total_price', 'message']);
    }
}
