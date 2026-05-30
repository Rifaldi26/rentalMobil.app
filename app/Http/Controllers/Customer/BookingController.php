<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\BookingConflictException;
use App\Exceptions\UnauthorizedBookingException;
use App\Exceptions\VehicleNotAvailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
    ) {}

    public function index(): View
    {
        $bookings = Booking::with(['vehicle.primaryPhoto', 'review'])
            ->where('user_id', auth()->id())
            ->filterStatus(request('status'))
            ->latest()
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function create(Vehicle $vehicle): View|RedirectResponse
    {
        if (! $vehicle->is_active || ! $vehicle->is_verified) {
            return redirect()->route('cars.index')
                ->with('error', 'Kendaraan ini tidak tersedia untuk disewa.');
        }

        $vehicle->load(['photos']);

        return view('customer.bookings.create', compact('vehicle'));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        try {
            ['booking' => $booking, 'paymentData' => $paymentData] =
                $this->bookingService->createBooking($request->user(), $request->validated());
        } catch (VehicleNotAvailableException $e) {
            return back()->with('error', $e->getMessage());
        } catch (BookingConflictException $e) {
            return back()->withInput()->withErrors(['start_date' => $e->getMessage()]);
        }

        return redirect()
            ->route('customer.bookings.pay', $booking)
            ->with('success', 'Pemesanan dibuat! Selesaikan pembayaran Anda.');
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $booking->load(['vehicle.photos', 'payment', 'messages.sender', 'review']);

        return view('customer.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        try {
            $this->bookingService->cancelByUser($booking, request()->user());
        } catch (UnauthorizedBookingException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pemesanan berhasil dibatalkan.');
    }

    public function pay(Booking $booking): View
    {
        $this->authorize('view', $booking);
        $booking->load(['vehicle', 'payment']);

        return view('customer.payment.checkout', compact('booking'));
    }

    public function receipt(Booking $booking): View
    {
        $this->authorize('view', $booking);
        $booking->load(['vehicle', 'payment', 'user']);

        return view('customer.bookings.receipt', compact('booking'));
    }
    
}