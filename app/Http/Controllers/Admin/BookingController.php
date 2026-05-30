<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService) {}

    public function show(Booking $booking): View
    {
        $booking->load(['vehicle.photos', 'user', 'payment', 'messages.sender', 'review']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function index(Request $request): View
    {
        $bookings = Booking::with(['user', 'vehicle'])
            ->filterStatus($request->status)
            ->filterBulan($request->bulan)
            ->search($request->search)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statusCounts = Booking::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.bookings.index', compact('bookings', 'statusCounts'));
    }

    public function confirm(Booking $booking): RedirectResponse
    {
        $this->bookingService->confirm($booking);

        return back()->with('success', "Pemesanan {$booking->booking_code} berhasil dikonfirmasi.");
    }

    public function activate(Booking $booking): RedirectResponse
    {
        $this->bookingService->activate($booking);

        return back()->with('success', "Pemesanan {$booking->booking_code} diaktifkan. Kendaraan diserahkan ke pelanggan.");
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate([
            'reason'        => ['required', 'string', 'max:300'],
            'reason_custom' => ['nullable', 'string', 'max:300'],
        ]);

        $reason = $request->reason === 'Lainnya'
            ? ($request->reason_custom ?: 'Ditolak oleh admin')
            : $request->reason;

        $this->bookingService->reject($booking, $reason);

        return back()->with('success', 'Pemesanan ditolak. Refund akan diproses otomatis.');
    }

    public function finish(Booking $booking): RedirectResponse
    {
        $this->bookingService->markFinished($booking);

        return back()->with('success', "Pemesanan {$booking->booking_code} selesai. Pendapatan dikreditkan ke saldo usaha.");
    }
}
