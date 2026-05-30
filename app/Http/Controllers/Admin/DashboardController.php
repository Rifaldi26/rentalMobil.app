<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\WithdrawalStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Withdrawal;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'revenue_month'    => Booking::where('status', BookingStatus::Selesai)
                ->whereMonth('updated_at', now()->month)->sum('total_price'),
            'revenue_growth'   => $this->revenueGrowth(),
            'bookings_month'   => Booking::whereMonth('created_at', now()->month)->count(),
            'pending_bookings' => Booking::where('status', BookingStatus::Pending)->count(),
            'total_users'      => User::customers()->count(),
            'new_users_month'  => User::customers()->whereMonth('created_at', now()->month)->count(),
            'total_vehicles'   => Vehicle::where('is_active', true)->count(),
            'pending_vehicles' => Vehicle::where('is_verified', false)->count(),
        ];

        $recentBookings     = Booking::with(['user', 'vehicle'])->latest()->take(10)->get();
        $pendingWithdrawals = Withdrawal::with('user')
            ->where('status', WithdrawalStatus::Pending)->latest()->take(5)->get();

        return view('admin.dashboard.index', compact(
            'stats', 'recentBookings', 'pendingWithdrawals'
        ));
    }

    private function revenueGrowth(): float
    {
        $thisMonth = Booking::where('status', BookingStatus::Selesai)
            ->whereMonth('updated_at', now()->month)->sum('total_price');

        $lastMonth = Booking::where('status', BookingStatus::Selesai)
            ->whereMonth('updated_at', now()->subMonth()->month)->sum('total_price');

        if ($lastMonth == 0) {
            return 0;
        }

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }
}