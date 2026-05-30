<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\PemesananStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    /** @test */
    public function admin_can_view_bookings_index(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.bookings.index'))
            ->assertOk()
            ->assertViewIs('admin.bookings.index');
    }

    /** @test */
    public function admin_can_confirm_pending_booking(): void
    {
        $booking = Booking::factory()->create(['status' => PemesananStatus::Pending]);

        $this->actingAs($this->admin)
            ->patch(route('admin.bookings.confirm', $booking))
            ->assertRedirect();

        $this->assertEquals(PemesananStatus::Dikonfirmasi, $booking->fresh()->status);
    }

    /** @test */
    public function admin_can_reject_booking_with_reason(): void
    {
        $booking = Booking::factory()->create(['status' => PemesananStatus::Pending]);

        $this->actingAs($this->admin)
            ->patch(route('admin.bookings.reject', $booking), [
                'reason' => 'Kendaraan tidak tersedia pada tanggal tersebut',
            ])
            ->assertRedirect();

        $this->assertEquals(PemesananStatus::Dibatalkan, $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->rejected_reason);
    }

    /** @test */
    public function customer_cannot_access_admin_routes(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $this->actingAs($customer)
            ->get(route('admin.bookings.index'))
            ->assertForbidden();
    }

    /** @test */
    public function guest_is_redirected_from_admin_routes(): void
    {
        $this->get(route('admin.bookings.index'))
            ->assertRedirect(route('login'));
    }
}
