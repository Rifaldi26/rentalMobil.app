<?php
namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCustomer();
    }

    public function rules(): array
    {
        return [
            'vehicle_id'      => ['required', 'integer', 'exists:vehicles,id'],
            'start_date'      => ['required', 'date', 'after_or_equal:today'],
            'end_date'        => ['required', 'date', 'after:start_date'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'end_date.after'            => 'Tanggal selesai harus setelah tanggal mulai.',
            'vehicle_id.exists'         => 'Kendaraan yang dipilih tidak ditemukan.',
        ];
    }
}
