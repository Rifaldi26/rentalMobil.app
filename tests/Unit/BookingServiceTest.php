<?php

namespace Tests\Unit;

use App\Enums\MobilStatus;
use App\Enums\PemesananStatus;
use App\Exceptions\MobilTidakTersediaException;
use App\Exceptions\PemesananKonflikException;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(BookingService::class);
    }

    /** @test */
    public function it_creates_booking_successfully(): void
    {
        $user  = User::factory()->create();
        $mobil = Mobil::factory()->create(['status' => MobilStatus::Tersedia, 'harga_per_hari' => 300_000]);

        $pemesanan = $this->service->createBooking($user, [
            'mobil_id'        => $mobil->id,
            'tanggal_mulai'   => '2026-07-01',
            'tanggal_selesai' => '2026-07-03',
        ]);

        $this->assertInstanceOf(Pemesanan::class, $pemesanan);
        $this->assertEquals(PemesananStatus::Pending, $pemesanan->status);
        $this->assertEquals(600_000, $pemesanan->total_harga); // 2 hari × 300.000
    }

    /** @test */
    public function it_throws_when_mobil_tidak_tersedia(): void
    {
        $this->expectException(MobilTidakTersediaException::class);

        $user  = User::factory()->create();
        $mobil = Mobil::factory()->create(['status' => MobilStatus::Disewa]);

        $this->service->createBooking($user, [
            'mobil_id'        => $mobil->id,
            'tanggal_mulai'   => '2026-07-01',
            'tanggal_selesai' => '2026-07-03',
        ]);
    }

    /** @test */
    public function it_throws_when_tanggal_konflik(): void
    {
        $this->expectException(PemesananKonflikException::class);

        $user  = User::factory()->create();
        $mobil = Mobil::factory()->create(['status' => MobilStatus::Tersedia]);

        // Booking pertama
        Pemesanan::factory()->create([
            'mobil_id'        => $mobil->id,
            'tanggal_mulai'   => '2026-07-01',
            'tanggal_selesai' => '2026-07-05',
            'status'          => PemesananStatus::Dikonfirmasi,
        ]);

        // Booking kedua dengan tanggal tumpang tindih
        $this->service->createBooking($user, [
            'mobil_id'        => $mobil->id,
            'tanggal_mulai'   => '2026-07-03',
            'tanggal_selesai' => '2026-07-07',
        ]);
    }

    /** @test */
    public function admin_can_confirm_booking(): void
    {
        $pemesanan = Pemesanan::factory()->create(['status' => PemesananStatus::Pending]);

        $updated = $this->service->confirm($pemesanan);

        $this->assertEquals(PemesananStatus::Dikonfirmasi, $updated->status);
        $this->assertEquals(MobilStatus::Disewa, $updated->mobil->status);
    }

    /** @test */
    public function admin_can_mark_booking_finished(): void
    {
        $pemesanan = Pemesanan::factory()->create(['status' => PemesananStatus::Dikonfirmasi]);

        $updated = $this->service->markFinished($pemesanan);

        $this->assertEquals(PemesananStatus::Selesai, $updated->status);
        $this->assertEquals(MobilStatus::Tersedia, $updated->mobil->status);
    }
}
