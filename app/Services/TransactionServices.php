<?php

namespace App\Services;

use App\Models\Category;
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

            $this->wallet->increment('balance', $validatedData['amount']);

            $category = $this->findFirstOrCreateCategory($validatedData);

            Transaction::create([
                'wallet_id' => $this->wallet->id,
                'amount' => $validatedData['amount'],
                'type' => 'entry',
                'description' => $validatedData['description'],
                'category_name' => $category->name,
                'date' => $validatedData['date'],
            ]);

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

            $this->wallet->decrement('balance', $validatedData['amount']);

            $category = $this->findFirstOrCreateCategory($validatedData);

            Transaction::create([
                'wallet_id' => $this->wallet->id,
                'amount' => $validatedData['amount'],
                'type' => 'withdraw',
                'description' => $validatedData['description'],
                'category_name' => $category->name,
                'date' => $validatedData['date'],
            ]);

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

    public function findFirstOrCreateCategory($validatedData)
    {

        if (! empty($validatedData['category_name'])) {

            $category = Category::firstOrCreate([
                'name' => mb_convert_case($validatedData['category_name'], 2),
                'isPersonalizada' => true,
                'user_id' => $this->wallet->user_id,
            ]);

            return $category;
        }
        $validatedData['description'] = mb_strtoupper($validatedData['description']);

        $plCategorias = [
            'Transporte' => [
                'TRANSPORTE', 'UBER', 'CARRO', 'VIAGEM', 'MOTO', '99', 'TAXI', 'TÁXI',
            ],
            'Alimentacao' => [
                'ALIMENTACAO', 'COMIDA', 'PIZZA', 'HAMBURGUER', 'RODIZIO', 'RODÍZIO', 'ALIMENTO', 'ALIMENTAÇÃO', 'MERCADO', 'MCDONALDS', 'MC DONALDS',
                'SUBWAY', "BOB'S", 'LANCHE', 'ALMOÇO', 'CAFE', 'CAFÉ',
            ],
            'Lazer' => [
                'LAZER', 'PASSEIO', 'CINEMA', 'RETIRO', 'ACAMPAMENTO', 'PIQUIQUE', 'SHOPPING',
            ],
            'Fianceiro' => [
                'FINANCEIRO', 'SALARIO', 'SALÁRIO', 'PAGAMENTO', 'DINHEIRO', 'RECEBIDO', 'ENVIADO', 'PIX', 'DEPOSITO', 'DEPÓSITO', 'SAQUE', 'RETIRADA', 'DEBITO', 'DÉBITO', 'CHEQUE', 'BOLETO', 'CARNÊ', 'PAGUEI', 'PAGO', 'RECEBI', 'ENVIEI',
            ],
        ];

        foreach ($plCategorias as $plCategoria => $categoria) {
            foreach ($categoria as $pl) {
                if (str_contains($validatedData['description'], $pl)) {
                    $category = Category::firstOrCreate([
                        'name' => mb_convert_case($plCategoria, 2),
                        'isPersonalizada' => false,
                        'user_id' => 0,
                    ]);

                    return $category;
                }
            }
        }

        $category = Category::firstOrCreate([
            'name' => 'Outros',
            'isPersonalizada' => false,
            'user_id' => 0,
        ]);

        return $category;
    }
}

// {
//   "amount": 1500,
//   "type": "entrada",
//   "description": "Salário Junho - Empresa X",
//   "date": "2025-06-05",
//   "category_id": 1
// }
