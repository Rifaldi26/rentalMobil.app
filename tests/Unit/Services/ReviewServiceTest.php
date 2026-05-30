<?php

namespace Tests\Unit\Services;

use App\Enums\PemesananStatus;
use App\Exceptions\UnauthorizedPemesananException;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReviewService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ReviewService::class);
    }

    /** @test */
    public function customer_can_review_completed_booking(): void
    {
        $user    = User::factory()->create();
        $booking = Booking::factory()->completed()->create(['user_id' => $user->id]);

        $review = $this->service->create($user, $booking, [
            'rating'  => 5,
            'comment' => 'Kendaraan sangat bersih dan mitra responsif!',
        ]);

        $this->assertInstanceOf(Review::class, $review);
        $this->assertEquals(5, $review->rating);
        $this->assertTrue($review->is_visible);
    }

    /** @test */
    public function throws_when_user_reviews_someone_elses_booking(): void
    {
        $this->expectException(UnauthorizedPemesananException::class);

        $owner   = User::factory()->create();
        $other   = User::factory()->create();
        $booking = Booking::factory()->completed()->create(['user_id' => $owner->id]);

        $this->service->create($other, $booking, ['rating' => 5]);
    }

    /** @test */
    public function throws_when_booking_not_completed(): void
    {
        $this->expectException(\RuntimeException::class);

        $user    = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status'  => PemesananStatus::Pending,
        ]);

        $this->service->create($user, $booking, ['rating' => 4]);
    }

    /** @test */
    public function vehicle_avg_rating_recalculated_after_review(): void
    {
        $user    = User::factory()->create();
        $booking = Booking::factory()->completed()->create(['user_id' => $user->id]);
        $vehicleId = $booking->vehicle_id;

        $this->service->create($user, $booking, ['rating' => 4]);

        $this->assertEquals(4.00, \App\Models\Vehicle::find($vehicleId)->avg_rating);
        $this->assertEquals(1, \App\Models\Vehicle::find($vehicleId)->review_count);
    }

    /** @test */
    public function admin_can_toggle_review_visibility(): void
    {
        $review = Review::factory()->create(['is_visible' => true]);

        $updated = $this->service->toggleVisibility($review);
        $this->assertFalse($updated->is_visible);

        $updated = $this->service->toggleVisibility($updated);
        $this->assertTrue($updated->is_visible);
    }
}
