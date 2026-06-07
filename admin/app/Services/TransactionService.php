<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;

class TransactionService
{
    public function createTransaction(User $user, string $type, float $amount, string $description = null)
    {
        $beforeBalance = $user->balance;
        $afterBalance = $beforeBalance + $amount;

        Transaction::create([
            'user_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'before_balance' => $beforeBalance,
            'after_balance' => $afterBalance,
            'description' => $description,
        ]);

        $user->update(['balance' => $afterBalance]);
    }
}
