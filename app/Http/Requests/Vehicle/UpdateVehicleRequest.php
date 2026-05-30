<?php

namespace App\Http\Requests\Vehicle;

use App\Enums\VehicleCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle')?->id;

        return [
            'category'        => ['required', new Enum(VehicleCategory::class)],
            'brand'           => ['required', 'string', 'max:80'],
            'model'           => ['required', 'string', 'max:80'],
            'year'            => ['required', 'integer', 'min:2000', 'max:' . date('Y')],
            'plate_number'    => ['required', 'string', 'max:20', "unique:vehicles,plate_number,{$vehicleId}"],
            'price_per_day'   => ['required', 'numeric', 'min:50000'],
            'deposit'         => ['nullable', 'numeric', 'min:0'],
            'capacity'        => ['required', 'integer', 'min:1', 'max:50'],
            'transmission'    => ['required', 'in:matic,manual'],
            'fuel_type'       => ['required', 'in:bensin,diesel,listrik'],
            'description'     => ['required', 'string', 'min:30', 'max:2000'],
            'rental_terms'    => ['nullable', 'string', 'max:2000'],
            'features'        => ['nullable', 'array'],
            'features.*'      => ['string', 'max:40'],
            'min_rental_days' => ['required', 'integer', 'min:1'],
            'max_rental_days' => ['required', 'integer', 'gte:min_rental_days'],
            'city'            => ['required', 'string', 'max:80'],
            'photos'          => ['nullable', 'array', 'max:8'],
            'photos.*'        => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ];
    }
}