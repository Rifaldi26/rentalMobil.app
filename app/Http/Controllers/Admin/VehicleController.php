<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Vehicle\UpdateVehicleRequest;
use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use App\Services\AuditLogService;
use App\Services\VehicleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function __construct(private readonly VehicleService $vehicleService) {}

    public function index(Request $request): View
    {
        $vehicles = Vehicle::query()
            ->withCount(['bookings', 'reviews'])
            ->when($request->status === 'pending', fn ($q) => $q->where('is_verified', false))
            ->when($request->status === 'active',  fn ($q) => $q->where('is_verified', true)->where('is_active', true))
            ->when($request->search, fn ($q) => $q->search($request->search))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create(): View
    {
        return view('admin.vehicles.create');
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $data   = $request->except('photos');
        $photos = $request->file('photos', []);

        $this->vehicleService->create($data, $photos);

        return redirect()
            ->route('admin.vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan!');
    }

    public function show(Vehicle $vehicle): View
    {
        $vehicle->load(['photos', 'reviews.user']);

        return view('admin.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle): View
    {
        $vehicle->load('photos');

        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $data      = $request->except('photos');
        $newPhotos = $request->file('photos', []);

        $this->vehicleService->update($vehicle, $data, $newPhotos);

        return redirect()
            ->route('admin.vehicles.index')
            ->with('success', 'Data kendaraan berhasil diperbarui!');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $this->vehicleService->delete($vehicle);

        AuditLogService::log('vehicle.deleted', $vehicle);

        return redirect()
            ->route('admin.vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }

    public function verify(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update(['is_verified' => true, 'verified_at' => now()]);

        AuditLogService::log('vehicle.verified', $vehicle);

        return back()->with('success', "Kendaraan {$vehicle->brand} {$vehicle->model} berhasil diverifikasi.");
    }

    public function reject(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $request->validate(['reason' => ['required', 'string', 'max:300']]);

        $vehicle->update(['is_verified' => false, 'is_active' => false]);

        AuditLogService::log('vehicle.rejected', $vehicle, [], ['reason' => $request->reason]);

        return back()->with('success', 'Kendaraan ditolak.');
    }

    public function toggleActive(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update(['is_active' => ! $vehicle->is_active]);

        return back()->with('success', 'Status kendaraan diperbarui.');
    }

    public function deletePhoto(Vehicle $vehicle, VehiclePhoto $photo): RedirectResponse
    {
        abort_unless($photo->vehicle_id === $vehicle->id, 403);

        $this->vehicleService->deletePhoto($photo);

        return back()->with('success', 'Foto dihapus.');
    }
}