<?php

namespace Tests\Unit\Services;

use App\Enums\PemesananStatus;
use App\Exceptions\MobilTidakTersediaException;
use App\Exceptions\PemesananKonflikException;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock PaymentService to avoid gateway calls in tests
        $paymentMock = Mockery::mock(PaymentService::class);
        $paymentMock->shouldReceive('createTransaction')
            ->andReturn(['snap_token' => 'test-token', 'redirect_url' => 'https://test.com']);
        $paymentMock->shouldReceive('refund')->andReturn(true);

        $this->service = new BookingService($paymentMock);
    }

    /** @test */
    public function it_creates_booking_with_correct_price_calculation(): void
    {
        $user    = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['price_per_day' => 300_000, 'deposit' => 150_000]);

        ['booking' => $booking] = $this->service->createBooking($user, [
            'vehicle_id' => $vehicle->id,
            'start_date' => '2026-08-01',
            'end_date'   => '2026-08-04', // 3 hari
        ]);

        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertEquals(PemesananStatus::Pending, $booking->status);
        $this->assertEquals(900_000, $booking->total_price);                    // 3 × 300.000
        $this->assertEquals(45_000,  $booking->service_fee);                   // 5% dari 900.000
        $this->assertEquals(150_000, $booking->deposit);
    }

    /** @test */
    public function it_throws_when_vehicle_not_verified(): void
    {
        $this->expectException(MobilTidakTersediaException::class);

        $user    = User::factory()->create();
        $vehicle = Vehicle::factory()->unverified()->create();

        $this->service->createBooking($user, [
            'vehicle_id' => $vehicle->id,
            'start_date' => '2026-08-01',
            'end_date'   => '2026-08-04',
        ]);
    }

    /** @test */
    public function it_throws_on_date_conflict(): void
    {
        $this->expectException(PemesananKonflikException::class);

        $user    = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['is_verified' => true, 'is_active' => true]);

        Booking::factory()->confirmed()->create([
            'vehicle_id' => $vehicle->id,
            'start_date' => '2026-08-01',
            'end_date'   => '2026-08-06',
        ]);

        $this->service->createBooking($user, [
            'vehicle_id' => $vehicle->id,
            'start_date' => '2026-08-03',
            'end_date'   => '2026-08-08',
        ]);
    }

    /** @test */
    public function confirm_updates_status(): void
    {
        $booking = Booking::factory()->create(['status' => PemesananStatus::Pending]);

        $updated = $this->service->confirm($booking);

        $this->assertEquals(PemesananStatus::Dikonfirmasi, $updated->status);
    }

    /** @test */
    public function mark_finished_credits_partner_balance(): void
    {
        $booking = Booking::factory()->confirmed()->create(['total_price' => 500_000]);

        $initialBalance = $booking->vehicle->partner->balance;
        $this->service->markFinished($booking);

        $newBalance = $booking->vehicle->partner->fresh()->balance;
        $this->assertEquals($initialBalance + 475_000, $newBalance); // 500.000 × 95%
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
