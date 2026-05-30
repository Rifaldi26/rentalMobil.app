<?php

namespace Tests\Unit\Services;

use App\Enums\WithdrawalStatus;
use App\Exceptions\InsufficientBalanceException;
use App\Models\Partner;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawalServiceTest extends TestCase
{
    use RefreshDatabase;

    private WithdrawalService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(WithdrawalService::class);
    }

    /** @test */
    public function partner_can_request_withdrawal(): void
    {
        $partner = Partner::factory()->create(['balance' => 500_000]);

        $wd = $this->service->request($partner, 200_000);

        $this->assertInstanceOf(Withdrawal::class, $wd);
        $this->assertEquals(WithdrawalStatus::Pending, $wd->status);
        $this->assertEquals(200_000, $wd->amount);
        $this->assertEquals(300_000, $partner->fresh()->balance); // saldo berkurang
    }

    /** @test */
    public function throws_when_balance_insufficient(): void
    {
        $this->expectException(InsufficientBalanceException::class);

        $partner = Partner::factory()->create(['balance' => 50_000]);
        $this->service->request($partner, 200_000);
    }

    /** @test */
    public function throws_when_below_minimum_amount(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $partner = Partner::factory()->create(['balance' => 1_000_000]);
        $this->service->request($partner, 50_000); // di bawah minimum 100.000
    }

    /** @test */
    public function admin_can_approve_withdrawal(): void
    {
        $partner = Partner::factory()->create(['balance' => 0]);
        $wd      = Withdrawal::factory()->create([
            'partner_id' => $partner->id,
            'amount'     => 200_000,
            'status'     => WithdrawalStatus::Pending,
        ]);

        $updated = $this->service->approve($wd, 'Transfer BCA selesai.');

        $this->assertEquals(WithdrawalStatus::Processed, $updated->status);
        $this->assertNotNull($updated->processed_at);
    }

    /** @test */
    public function admin_reject_returns_balance_to_partner(): void
    {
        $partner = Partner::factory()->create(['balance' => 100_000]);
        $wd      = Withdrawal::factory()->create([
            'partner_id' => $partner->id,
            'amount'     => 300_000,
            'status'     => WithdrawalStatus::Pending,
        ]);

        $this->service->reject($wd, 'Rekening tidak valid.');

        $this->assertEquals(WithdrawalStatus::Rejected, $wd->fresh()->status);
        $this->assertEquals(400_000, $partner->fresh()->balance); // 100.000 + 300.000 dikembalikan
    }
}
