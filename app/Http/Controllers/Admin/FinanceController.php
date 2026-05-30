<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Withdrawal;
use App\Enums\BookingStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function index(Request $request): View
    {
        $revenue = Booking::where('status', BookingStatus::Selesai)
            ->filterBulan($request->bulan)
            ->sum('total_price');

        $withdrawals = Withdrawal::with('user')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.finance.index', compact('revenue', 'withdrawals'));
    }
}