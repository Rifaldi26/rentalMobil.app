<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Services\ScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(private readonly ScheduleService $scheduleService) {}

    /**
     * Tampilkan halaman jadwal kendaraan dengan kalender interaktif.
     */
    public function index(?Vehicle $vehicle = null): View|RedirectResponse
    {
        if (!$vehicle) {
            $vehicle = Vehicle::first();
            if (!$vehicle) {
                return redirect()->route('admin.vehicles.index')
                    ->with('error', 'Belum ada kendaraan terdaftar.');
            }
        }

        return view('admin.schedule.index', compact('vehicle'));
    }
    /**
     * API endpoint untuk kalender UI — kembalikan data availability dalam format JSON.
     */
    public function availability(Request $request, Vehicle $vehicle): JsonResponse
    {
        $year  = (int) ($request->year  ?? now()->year);
        $month = (int) ($request->month ?? now()->month);

        $data = $this->scheduleService->getAvailabilityData($vehicle, $year, $month);

        return response()->json($data);
    }

    /**
     * Blokir satu atau beberapa tanggal.
     */
    public function block(Request $request, Vehicle $vehicle): JsonResponse|RedirectResponse
    {
        $request->validate([
            'dates'   => ['required', 'array', 'min:1'],
            'dates.*' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'reason'  => ['nullable', 'string', 'max:100'],
        ]);

        $result = $this->scheduleService->blockDates(
            $vehicle,
            $request->dates,
            $request->reason
        );

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        $msg = "{$result['blocked']} tanggal berhasil diblokir.";
        if ($result['skipped'] > 0) {
            $msg .= " {$result['skipped']} tanggal dilewati karena sudah ada pemesanan aktif.";
        }

        return back()->with('success', $msg);
    }

    /**
     * Blokir rentang tanggal sekaligus (mis. untuk servis kendaraan).
     */
    public function blockRange(Request $request, Vehicle $vehicle): JsonResponse|RedirectResponse
    {
        $request->validate([
            'start_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'end_date'   => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'reason'     => ['nullable', 'string', 'max:100'],
        ]);

        $result = $this->scheduleService->blockDateRange(
            $vehicle,
            $request->start_date,
            $request->end_date,
            $request->reason
        );

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with('success', "{$result['blocked']} tanggal berhasil diblokir.");
    }

    /**
     * Hapus blokir satu tanggal.
     */
    public function unblock(Vehicle $vehicle, string $date): JsonResponse|RedirectResponse
    {
        $deleted = $this->scheduleService->unblockDate($vehicle, $date);

        if (request()->expectsJson()) {
            return response()->json(['success' => $deleted]);
        }

        return back()->with(
            $deleted ? 'success' : 'error',
            $deleted ? 'Blokir tanggal berhasil dihapus.' : 'Tanggal tidak ditemukan di daftar blokir.'
        );
    }
}
