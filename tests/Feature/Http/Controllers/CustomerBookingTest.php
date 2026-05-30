<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\PemesananStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerBookingTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create(['role' => UserRole::Customer]);
    }

    /** @test */
    public function customer_can_view_booking_form(): void
    {
        $vehicle = Vehicle::factory()->create(['is_verified' => true, 'is_active' => true]);

        $this->actingAs($this->customer)
            ->get(route('customer.bookings.create', $vehicle))
            ->assertOk()
            ->assertViewIs('customer.bookings.create');
    }

    /** @test */
    public function customer_can_view_own_bookings(): void
    {
        Booking::factory()->count(3)->create(['user_id' => $this->customer->id]);

        $this->actingAs($this->customer)
            ->get(route('customer.bookings.index'))
            ->assertOk()
            ->assertViewIs('customer.bookings.index');
    }

    /** @test */
    public function customer_cannot_view_others_booking(): void
    {
        $other   = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $other->id]);

        $this->actingAs($this->customer)
            ->get(route('customer.bookings.show', $booking))
            ->assertForbidden();
    }

    /** @test */
    public function customer_can_cancel_pending_booking(): void
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->customer->id,
            'status'  => PemesananStatus::Pending,
        ]);

        $this->actingAs($this->customer)
            ->patch(route('customer.bookings.cancel', $booking))
            ->assertRedirect();

        $this->assertEquals(PemesananStatus::Dibatalkan, $booking->fresh()->status);
    }

    /** @test */
    public function customer_cannot_cancel_confirmed_booking(): void
    {
        $booking = Booking::factory()->confirmed()->create([
            'user_id' => $this->customer->id,
        ]);

        $this->actingAs($this->customer)
            ->patch(route('customer.bookings.cancel', $booking))
            ->assertRedirect(); // redirects back with error

        // Status tidak berubah
        $this->assertEquals(PemesananStatus::Dikonfirmasi, $booking->fresh()->status);
    }

    /** @test */
    public function guest_is_redirected_to_login(): void
    {
        $vehicle = Vehicle::factory()->create();

        $this->get(route('customer.bookings.create', $vehicle))
            ->assertRedirect(route('login'));
    }
}
