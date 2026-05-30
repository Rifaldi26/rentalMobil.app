<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VehicleService
{
    public function create(array $data, array $photos = []): Vehicle
    {
        return DB::transaction(function () use ($data, $photos) {
            $vehicle = Vehicle::create($data);
            $this->savePhotos($vehicle, $photos);
            return $vehicle;
        });
    }

    public function update(Vehicle $vehicle, array $data, array $newPhotos = []): Vehicle
    {
        return DB::transaction(function () use ($vehicle, $data, $newPhotos) {
            $vehicle->update($data);

            if ($newPhotos) {
                $this->savePhotos($vehicle, $newPhotos);
            }

            return $vehicle->fresh(['photos']);
        });
    }

    public function delete(Vehicle $vehicle): void
    {
        DB::transaction(function () use ($vehicle) {
            foreach ($vehicle->photos as $photo) {
                Storage::disk('public')->delete($photo->path);
            }
            $vehicle->photos()->delete();
            $vehicle->delete();
        });
    }

    public function setPrimaryPhoto(Vehicle $vehicle, int $photoId): void
    {
        $vehicle->photos()->update(['is_primary' => false]);
        $vehicle->photos()->where('id', $photoId)->update(['is_primary' => true]);
    }

    public function deletePhoto(VehiclePhoto $photo): void
    {
        Storage::disk('public')->delete($photo->path);
        $photo->delete();
    }

    public function toggleActive(Vehicle $vehicle): Vehicle
    {
        $vehicle->update(['is_active' => ! $vehicle->is_active]);
        return $vehicle->fresh();
    }

    private function savePhotos(Vehicle $vehicle, array $photos): void
    {
        $existingCount = $vehicle->photos()->count();

        foreach ($photos as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path      = $file->store("vehicles/{$vehicle->id}", 'public');
            $isPrimary = ($existingCount === 0 && $index === 0);

            $vehicle->photos()->create([
                'path'       => $path,
                'is_primary' => $isPrimary,
                'order'      => $existingCount + $index,
            ]);

            $existingCount++;
        }
    }
}