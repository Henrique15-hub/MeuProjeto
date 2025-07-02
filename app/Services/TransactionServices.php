<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionServices
{
    private $wallet;

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->wallet = $user->wallet()->first();
    }

    public function entry($validatedData): array
    {
        try {
            DB::beginTransaction();

            $category = CategoryServices::findFirstOrCreateCategory($validatedData, $this->user);

            Transaction::create([
                'wallet_id' => $this->wallet->id,
                'amount' => $validatedData['amount'],
                'type' => 'entry',
                'description' => $validatedData['description'],
                'category_name' => $category->name,
                'date' => $validatedData['date'],
            ]);

            $this->wallet->increment('balance', $validatedData['amount']);
            DB::commit();

            return [
                'success' => true,
                'message' => 'entry success',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function withdraw($validatedData): array
    {
        try {
            DB::beginTransaction();

            $category = CategoryServices::findFirstOrCreateCategory($validatedData, $this->user);

            Transaction::create([
                'wallet_id' => $this->wallet->id,
                'amount' => $validatedData['amount'],
                'type' => 'withdraw',
                'description' => $validatedData['description'],
                'category_name' => $category->name,
                'date' => $validatedData['date'],
            ]);

            $this->wallet->decrement('balance', $validatedData['amount']);
            DB::commit();

            return [
                'success' => true,
                'message' => 'withdraw success',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
