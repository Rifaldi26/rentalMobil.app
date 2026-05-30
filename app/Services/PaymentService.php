<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Events\PaymentConfirmed;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Buat transaksi Midtrans Snap dan simpan record Payment.
     *
     * @return array{snap_token: string, redirect_url: string}
     * @throws \Exception jika Midtrans API gagal
     */
    public function createTransaction(Booking $booking): array
    {
        $booking->load(['user', 'vehicle']);

        $snapToken = $this->getMidtransSnapToken($booking);

        Payment::create([
            'booking_id'   => $booking->id,
            'gateway_name' => 'midtrans',
            'gateway_ref'  => $booking->booking_code,
            'snap_token'   => $snapToken,
            'amount'       => $booking->grand_total,
            'status'       => PaymentStatus::Pending,
        ]);

        return [
            'snap_token'   => $snapToken,
            'redirect_url' => config('services.midtrans.snap_url') . '?token=' . $snapToken,
        ];
    }

    /**
     * Proses callback webhook dari Midtrans atau Xendit.
     * Dipanggil dari PaymentController setelah verifikasi signature.
     *
     * @throws \Exception
     */
    public function handleWebhook(array $payload, string $gateway): void
    {
        $gatewayRef = $payload['order_id'] ?? $payload['external_id'] ?? null;

        $payment = Payment::where('gateway_ref', $gatewayRef)->firstOrFail();
        $booking = $payment->booking;

        $status = $this->resolvePaymentStatus($payload, $gateway);

        DB::transaction(function () use ($payment, $booking, $status, $payload, $gateway) {
            $payment->update([
                'status'          => $status,
                'method'          => $payload['payment_type'] ?? $payload['payment_method'] ?? null,
                'gateway_payload' => $payload,
                'paid_at'         => $status->isPaid() ? now() : null,
            ]);

            if ($status->isPaid()) {
                $booking->update(['payment_status' => PaymentStatus::Paid]);
                event(new PaymentConfirmed($booking));
            }

            if ($status->isFailed()) {
                $booking->update([
                    'status'         => BookingStatus::Dibatalkan,
                    'payment_status' => $status,
                ]);
            }
        });

        Log::info('Payment webhook processed', [
            'booking_id' => $booking->id,
            'status'     => $status->value,
            'gateway'    => $gateway,
        ]);
    }

    /**
     * Proses refund ke pelanggan via Midtrans Refund API.
     * Dipanggil saat booking ditolak atau dibatalkan.
     */
    public function refund(Booking $booking): bool
    {
        $payment = $booking->payment;

        if (! $payment || ! $payment->status->isPaid()) {
            return false;
        }

        try {
            $this->callMidtransRefund($payment->gateway_ref, (int) $payment->amount);

            $payment->update(['status' => PaymentStatus::Refunded]);
            $booking->update(['payment_status' => PaymentStatus::Refunded]);

            Log::info('Refund berhasil diproses', [
                'booking_id'  => $booking->id,
                'gateway_ref' => $payment->gateway_ref,
                'amount'      => $payment->amount,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Refund gagal', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);

            // Tandai sebagai pending manual — admin harus proses manual
            $payment->update(['status' => PaymentStatus::Pending]);

            throw $e;
        }
    }

    // ─── Private — Midtrans Integration ──────────────────────────

    private function getMidtransSnapToken(Booking $booking): string
    {
        $this->configureMidtrans();

        $params = [
            'transaction_details' => [
                'order_id'     => $booking->booking_code,
                'gross_amount' => (int) $booking->grand_total,
            ],
            'customer_details' => [
                'first_name' => $booking->user->name,
                'email'      => $booking->user->email,
                'phone'      => $booking->user->no_hp ?? '',
            ],
            'item_details' => [
                [
                    'id'       => 'RENTAL-' . $booking->vehicle_id,
                    'price'    => (int) $booking->total_price,
                    'quantity' => 1,
                    'name'     => "{$booking->vehicle->brand} {$booking->vehicle->model} ({$booking->duration_days} hari)",
                ],
                [
                    'id'       => 'DEPOSIT-' . $booking->id,
                    'price'    => (int) $booking->deposit,
                    'quantity' => 1,
                    'name'     => 'Deposit kendaraan (dikembalikan setelah sewa selesai)',
                ],
                [
                    'id'       => 'SERVICE-FEE-' . $booking->id,
                    'price'    => (int) $booking->service_fee,
                    'quantity' => 1,
                    'name'     => 'Biaya layanan platform',
                ],
            ],
            'callbacks' => [
                'finish' => route('payment.finish', $booking),
            ],
            'expiry' => [
                'unit'     => 'hours',
                'duration' => 4,  // kadaluwarsa setelah 4 jam — sama dengan batas konfirmasi admin
            ],
        ];

        return \Midtrans\Snap::getSnapToken($params);
    }

    private function callMidtransRefund(string $gatewayRef, int $amount): void
    {
        $this->configureMidtrans();

        // Midtrans Refund API
        \Midtrans\Transaction::refund($gatewayRef, [
            'refund_key' => 'refund-' . $gatewayRef . '-' . time(),
            'amount'     => $amount,
            'reason'     => 'Pesanan dibatalkan / ditolak',
        ]);
    }

    private function configureMidtrans(): void
    {
        \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;
    }

    private function resolvePaymentStatus(array $payload, string $gateway): PaymentStatus
    {
        if ($gateway === 'midtrans') {
            return match ($payload['transaction_status'] ?? '') {
                'capture', 'settlement'          => PaymentStatus::Paid,
                'pending'                         => PaymentStatus::Processing,
                'cancel', 'deny'                  => PaymentStatus::Failed,
                'expire'                          => PaymentStatus::Expired,
                default                           => PaymentStatus::Pending,
            };
        }

        // Xendit
        return match ($payload['status'] ?? '') {
            'PAID', 'SETTLED'        => PaymentStatus::Paid,
            'PENDING'                => PaymentStatus::Processing,
            'EXPIRED'                => PaymentStatus::Expired,
            'FAILED'                 => PaymentStatus::Failed,
            default                  => PaymentStatus::Pending,
        };
    }
}
