<?php
namespace App\Http\Requests\Withdrawal;

use Illuminate\Foundation\Http\FormRequest;

class StoreWithdrawalRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->isPartner(); }

    public function rules(): array
    {
        return [
            'amount'      => ['required', 'numeric', 'min:100000'],
            'bank_name'   => ['required', 'string', 'max:60'],
            'bank_account'=> ['required', 'string', 'max:30'],
            'bank_holder' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Minimum penarikan adalah Rp 100.000.',
        ];
    }
}
