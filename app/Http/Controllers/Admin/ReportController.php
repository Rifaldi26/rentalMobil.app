<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = (int) $request->get('period', 30);
        $from   = now()->subDays($period);

        $totalRevenue = Booking::where('status', BookingStatus::Selesai)
            ->where('updated_at', '>=', $from)
            ->sum('total_price');

        $totalBookings = Booking::where('created_at', '>=', $from)->count();

        $completedBookings = Booking::where('status', BookingStatus::Selesai)
            ->where('created_at', '>=', $from)
            ->count();

        $newUsers    = User::where('created_at', '>=', $from)->count();
        $newVehicles = Vehicle::where('created_at', '>=', $from)->count();

        $conversionRate = $totalBookings > 0
            ? round($completedBookings / $totalBookings * 100, 1)
            : 0;

        $dailyBookings = collect(range(13, 0))->map(fn ($d) => [
            'date'  => now()->subDays($d)->format('d/m'),
            'count' => Booking::whereDate('created_at', now()->subDays($d))->count(),
        ]);

        $topVehicles = Vehicle::withCount([
            'bookings' => fn ($q) => $q->where('created_at', '>=', $from),
        ])->orderByDesc('bookings_count')->take(5)->get();

        $byCity = Booking::join('vehicles', 'bookings.vehicle_id', '=', 'vehicles.id')
            ->where('bookings.status', BookingStatus::Selesai)
            ->where('bookings.updated_at', '>=', $from)
            ->selectRaw('vehicles.city, SUM(bookings.total_price) as revenue, COUNT(*) as count')
            ->groupBy('vehicles.city')
            ->orderByDesc('revenue')
            ->take(8)
            ->get();

        return view('admin.reports.index', compact(
            'period', 'totalRevenue', 'totalBookings', 'newUsers',
            'newVehicles', 'conversionRate', 'dailyBookings', 'topVehicles', 'byCity'
        ));
    }
}