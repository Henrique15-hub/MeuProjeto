<?php

namespace App\Http\Controllers;

use App\Http\Requests\services\TransactionRequest;
use App\Http\Requests\services\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\TransactionQueryServices;
use App\Services\TransactionServices;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    private $transactionservices;

    private $transactionqueryservices;

    private function getUserWallet()
    {
        return auth()->user()->wallet()->first();
    }

    public function __construct()
    {
        // $this->transactionservices = new TransactionServices(auth()->user());
        // tirar o auth user do construct (ou passar para um middleware interno)
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

        if ($response['success']) {
            return response()->json($response);
        }

        return response()->json($response, 500);
    }

    public function withdraw(TransactionRequest $data): JsonResponse
    {
        $validatedData = $data->validated();

        $this->transactionservices = new TransactionServices(auth()->user());
        $response = $this->transactionservices->withdraw($validatedData);

        if ($response['success']) {
            return response()->json($response);
        }

        return response()->json($response, 500);
    }

    public function update(UpdateTransactionRequest $request, int $id): JsonResponse
    {
        $validatedData = $request->validated();
        $wallet = $this->getUserWallet();

        $transacion = Transaction::where('wallet_id', $wallet->id)
            ->first();

        if (! $transacion) {
            return response()->json([
                'message' => 'transaction not found',
            ], 404);
        }

        $transacion->update($validatedData);

        return response()->json([
            'message' => 'transaction updated with success',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $wallet = $this->getUserWallet();

        $transaction = Transaction::where('wallet_id', $wallet->id)->first();

        if (! $transaction) {
            return response()->json([
                'message' => 'transaction not found',
            ], 404);
        }

        $transaction->delete();

        return response()->json([
            'message' => 'transaction deleted with success',
        ]);
    }

    public function queryData($initialData, $finalData): JsonResponse
    {
        $this->transactionqueryservices = new TransactionQueryServices;

        $queryData = $this->transactionqueryservices->queryData($initialData, $finalData);

        return response()->json([
            'message' => 'showing all transactions from day '.$initialData.' to day '.$finalData,
            'transactions' => $queryData,
        ]);
    }

    public function queryType(string $type): JsonResponse
    {
        $this->transactionqueryservices = new TransactionQueryServices;

        $type = strtolower($type);

        if (! in_array($type, ['entry', 'withdraw'])) {
            return response()->json([
                'message' => 'Inválid type',
                'Valid Types' => 'entry, withdraw',
            ], 422);
        }

        $queryType = $this->transactionqueryservices->queryType($type);

        return response()->json([
            'message' => 'showing all the transactions of type '.$type,
            'transactions' => $queryType,
        ]);
    }

    public function queryCategory(string $category)
    {
        $this->transactionqueryservices = new TransactionQueryServices;

        $categories = Category::whereIn('user_id', [0, auth()->id()])
            ->pluck('name');

        if (! in_array($category, $categories->toArray())) {
            return response()->json([
                'message' => "the category '".$category."' does not exist",
                'Your categories' => $categories,
            ], 422);
        }

        $tqs = $this->transactionqueryservices->queryCategory($category);

        return response()->json([
            'message' => "showing all transactions of the category '".$category."'",
            'transactions' => $tqs,
        ]);
    }
}

// fazer um filtro pra pegar as transações

// {
// 	"amount": 1500,
// 	"type": "retirada",
// 	"description": "Salário Junho - Empresa X",
// 	"date": "2025-06-05",
// 	"category_id": 1
// }
