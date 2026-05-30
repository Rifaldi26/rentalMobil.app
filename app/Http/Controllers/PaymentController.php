<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    /**
     * Midtrans webhook endpoint (no auth required — validated via signature).
     */
    public function midtransWebhook(Request $request): JsonResponse
    {
        try {
            $this->paymentService->handleWebhook($request->all(), 'midtrans');
        } catch (\Throwable $e) {
            Log::error('Midtrans webhook error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'error'], 500);
        }

        return response()->json(['message' => 'ok']);
    }

    /**
     * Xendit webhook endpoint.
     */
    public function xenditWebhook(Request $request): JsonResponse
    {
        try {
            $this->paymentService->handleWebhook($request->all(), 'xendit');
        } catch (\Throwable $e) {
            Log::error('Xendit webhook error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'error'], 500);
        }

        return response()->json(['message' => 'ok']);
    }

    /**
     * Callback dari Midtrans (redirect setelah bayar di Snap).
     */
    public function finish(Booking $booking): RedirectResponse
    {
        $booking->refresh();

        if ($booking->payment_status?->value === 'paid') {
            return redirect()
                ->route('customer.bookings.show', $booking)
                ->with('success', 'Pembayaran berhasil! Menunggu konfirmasi mitra.');
        }

        return redirect()
            ->route('customer.bookings.pay', $booking)
            ->with('error', 'Pembayaran belum berhasil. Silakan coba lagi.');
    }
}
