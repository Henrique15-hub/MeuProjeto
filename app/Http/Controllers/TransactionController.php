<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\services\TransactionRequest;
use App\Http\Requests\services\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\TransactionQueryServices;
use App\Services\TransactionServices;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    private $transactionservices;

    private $transactionqueryservices;

    public function __construct()
    {
        $this->transactionqueryservices = new TransactionQueryServices;
    }

    private function getUserWallet()
    {
        return auth()->user()->wallet()->first();
    }

    public function index(): JsonResponse
    {
        $wallet = $this->getUserWallet();

        $transactions = Transaction::where('wallet_id', $wallet->id)->get();

        return response()->json([
            'message' => 'showing all the transactions of the auth user',
            'transacions' => $transactions,
        ]);
    }

    public function entry(TransactionRequest $data): JsonResponse
    {
        $validatedData = $data->validated();

        $this->transactionservices = new TransactionServices(auth()->user());
        $response = $this->transactionservices->entry($validatedData);

        if (! $response['success']) {
            return response()->json($response, 500);
        }

        return response()->json($response, 201);
    }

    public function withdraw(TransactionRequest $data): JsonResponse
    {
        $validatedData = $data->validated();

        $this->transactionservices = new TransactionServices(auth()->user());
        $response = $this->transactionservices->withdraw($validatedData);

        if (! $response['success']) {
            return response()->json($response, 500);
        }

        return response()->json($response, 201);
    }

    public function update(UpdateTransactionRequest $request, int $id): JsonResponse
    {
        $validatedData = $request->validated();
        $wallet = $this->getUserWallet();

        $transaction = Transaction::where('wallet_id', $wallet->id)
            ->where('id', $id)
            ->first();

        if (! $transaction) {
            return response()->json([
                'message' => 'transaction not found',
            ], 404);
        }

        $transaction->update($validatedData);

        return response()->json([
            'message' => 'transaction updated with success',
            'transaction' => $transaction->fresh(),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $wallet = $this->getUserWallet();

        $transaction = Transaction::where('wallet_id', $wallet->id)
            ->where('id', $id)
            ->first();

        if (! $transaction) {
            return response()->json([
                'message' => 'transaction not found',
            ], 404);
        }

        $transaction->delete();

        return response()->json([
            'message' => 'transaction successfully deleted',
        ]);
    }

    public function queryDate($initialData, $finalData): JsonResponse
    {
        if (! Carbon::hasFormat($initialData, 'Y-m-d') or ! Carbon::hasFormat($finalData, 'Y-m-d')) {
            return ResponseHelper::withTip('invalid format or date', [], 400);
        }

        if (strtotime($initialData) > strtotime($finalData)) {
            return ResponseHelper::withTip('initial date has to be smaller than final date', [], 400);
        }

        $queryDate = $this->transactionqueryservices->queryDate($initialData, $finalData);

        return response()->json([
            'message' => "showing all transactions from {$initialData} to {$finalData}",
            'transactions' => $queryDate,
        ]);
    }

    public function queryType(string $type): JsonResponse
    {
        $type = strtolower($type);

        if (! in_array($type, ['entry', 'withdraw'])) {
            return response()->json([
                'message' => 'Invalid type',
                'Valid Types' => 'entry, withdraw',
            ], 422);
        }

        $queryType = $this->transactionqueryservices->queryType($type);

        return response()->json([
            'message' => "showing all the transactions of type {$type}",
            'transactions' => $queryType,
        ]);
    }

    public function queryCategory(string $category): JsonResponse
    {
        $categories = Category::whereIn('user_id', [0, auth()->id()])
            ->pluck('name');

        if (! in_array($category, $categories->toArray())) {
            return response()->json([
                'message' => "the category {$category} does not exist",
                'Your categories' => $categories,
            ], 422);
        }

        $tqs = $this->transactionqueryservices->queryCategory($category);

        return response()->json([
            'message' => "showing all transactions of the category {$category}",
            'transactions' => $tqs,
        ]);
    }
}
