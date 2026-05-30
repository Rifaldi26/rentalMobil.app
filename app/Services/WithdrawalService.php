<?php

namespace App\Services;

use App\Enums\WithdrawalStatus;
use App\Exceptions\InsufficientBalanceException;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

class WithdrawalService
{
    public const MINIMUM_WITHDRAWAL = 100000;
    public function create(User $user, array $data): Withdrawal
    {
        
        if ($user->balance < $data['amount']) {
            throw new InsufficientBalanceException(
                "Saldo tidak mencukupi. Saldo Anda: {$user->balance_formatted}."
            );
        }

        return DB::transaction(function () use ($user, $data) {
            $user->debitBalance($data['amount']);

            return Withdrawal::create([
                'user_id'     => $user->id,
                'amount'      => $data['amount'],
                'bank_account' => $data['bank_account'],
                'bank_name'   => $data['bank_name'],
                'bank_holder' => $data['bank_holder'],
                'status'      => WithdrawalStatus::Pending,
            ]);
        });
    }

    public function approve(Withdrawal $withdrawal, string $note = null): Withdrawal
    {
        abort_unless($withdrawal->status === WithdrawalStatus::Pending, 422, 'Hanya withdrawal pending yang bisa diproses.');

        $withdrawal->update([
            'status'       => WithdrawalStatus::Approved,
            'admin_note'   => $note,
            'processed_at' => now(),
        ]);

        return $withdrawal->fresh();
    }

    public function reject(Withdrawal $withdrawal, string $reason): Withdrawal
    {
        abort_unless($withdrawal->status === WithdrawalStatus::Pending, 422, 'Hanya withdrawal pending yang bisa ditolak.');

        DB::transaction(function () use ($withdrawal, $reason) {
            // Kembalikan saldo ke user
            $withdrawal->user->creditBalance($withdrawal->amount);

            $withdrawal->update([
                'status'       => WithdrawalStatus::Rejected,
                'admin_note'   => $reason,
                'processed_at' => now(),
            ]);
        });

        return $withdrawal->fresh();
    }
}