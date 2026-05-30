<?php
// app/Http/Controllers/Customer/ReviewController.php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\Booking;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviewService) {}

    public function create(Booking $booking): View|RedirectResponse
    {
        $this->authorize('view', $booking);

        if (! $booking->isReviewable()) {
            return back()->with('error', 'Booking ini tidak dapat diulas.');
        }

        return view('customer.reviews.create', compact('booking'));
    }

    public function store(StoreReviewRequest $request, Booking $booking): RedirectResponse
    {
        try {
            $this->reviewService->create(auth()->user(), $booking, $request->validated());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('customer.bookings.index')
            ->with('success', 'Ulasan berhasil dikirim. Terima kasih!');
    }
}
