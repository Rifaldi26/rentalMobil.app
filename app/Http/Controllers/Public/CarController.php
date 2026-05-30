<?php
namespace App\Http\Controllers\Public;

use App\Enums\VehicleCategory;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarController extends Controller
{
    public function index(Request $request): View
    {
        $vehicles = Vehicle::published()
            ->with(['primaryPhoto'])
            ->byCategory($request->category)
            ->byCity($request->city)
            ->priceRange($request->min_price, $request->max_price)
            ->search($request->search)
            ->when($request->sort === 'price_asc',  fn ($q) => $q->orderBy('price_per_day'))
            ->when($request->sort === 'price_desc', fn ($q) => $q->orderByDesc('price_per_day'))
            ->when($request->sort === 'rating',     fn ($q) => $q->orderByDesc('avg_rating'))
            ->when(! $request->sort,                fn ($q) => $q->orderByDesc('review_count'))
            ->paginate(12)
            ->withQueryString();

        $categories = VehicleCategory::cases();

        return view('public.cars.index', compact('vehicles', 'categories'));
    }

    public function show(Vehicle $vehicle): View
    {
        abort_unless($vehicle->is_active && $vehicle->is_verified, 404);

        $vehicle->load([
            'photos',
            'reviews' => fn ($q) => $q->with('user')->latest()->take(10),
        ]);

        $similarVehicles = Vehicle::published()
            ->where('id', '!=', $vehicle->id)
            ->where('category', $vehicle->category)
            ->where('city', $vehicle->city)
            ->with('primaryPhoto')
            ->take(4)
            ->get();

        return view('public.cars.show', compact('vehicle', 'similarVehicles'));
    }

    public function availability(Vehicle $vehicle, Request $request)
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
        ]);

        $available = $vehicle->isAvailableOn($request->start_date, $request->end_date);

        $days  = now()->parse($request->start_date)->diffInDays($request->end_date);
        $total = $days * $vehicle->price_per_day;

        return response()->json([
            'available'     => $available,
            'days'          => $days,
            'total_price'   => $total,
            'total_display' => 'Rp ' . number_format($total, 0, ',', '.'),
            'message'       => $available
                ? "Tersedia untuk {$days} hari"
                : 'Kendaraan tidak tersedia pada tanggal tersebut',
        ]);
    }
}
