<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_all_his_transactions()
    {
        $user = User::factory()->create();

        $otherUser = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Transaction::factory()->count(5)->create();

        Transaction::factory()->count(2)->create([
            'wallet_id' => $otherUser->id,
        ]);

        $this->assertDatabaseCount(Transaction::class, 7);

        $this->getJson(route('transaction-index'))
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'transactions',
            ])
            ->assertJsonCount(5, 'transactions');
    }

    public function test_user_can_create_entry_transaction(): void
    {
        $user = User::factory()->create();

        $walletId = $user->wallet->id;

        $description = 'test_user_can_create_entry_transaction';

        $this->actingAs($user, 'sanctum');

        $transaction = [
            'wallet_id' => $walletId,
            'amount' => 1550,
            'type' => 'entry',
            'description' => $description,
            'category_name' => 'test',
            'date' => '2025-01-01',
        ];

        $this->postJson(route('transaction-entry'), $transaction)
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertDatabaseHas(Transaction::class, [
            'wallet_id' => $walletId,
            'amount' => 1550,
            'type' => 'entry',
            'description' => $description,
            'category_name' => 'Test',
            'date' => '2025-01-01',

        ]);
    }

    public function test_user_can_create_withdraw_transaction()
    {
        $user = User::factory()->create();

        $walletId = $user->wallet->id;

        $transaction = [
            'wallet_id' => $walletId,
            'amount' => 1550,
            'type' => 'withdraw',
            'description' => 'test_user_can_create_withdraw_transaction',
            'category_name' => 'test',
            'date' => '2025-01-01',
        ];

        $this->actingAs($user, 'sanctum');

        $this->postJson(route('transaction-withdraw'), $transaction)
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertDatabaseHas(Transaction::class, [
            'wallet_id' => $walletId,
            'amount' => 1550,
            'type' => 'withdraw',
            'description' => 'test_user_can_create_withdraw_transaction',
            'category_name' => 'Test',
            'date' => '2025-01-01',
        ]);
    }

    public function test_user_can_update_transction()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $oldTransaction = Transaction::factory()->create();

        $amount = fake()->numberBetween(100, 10000);

        $description = 'test_user_can_update_transction';

        $date = fake()->date();

        $transaction = [
            'amount' => $amount,
            'type' => 'entry',
            'description' => $description,
            'category_name' => 'test',
            'date' => $date,
        ];

        $this->putJson(route('transaction-update', $oldTransaction->id), $transaction)
            ->assertOK()
            ->assertJsonStructure([
                'message',
                'transaction',
            ])
            ->assertJsonFragment([
                'amount' => $amount,
                'type' => 'entry',
                'description' => $description,
                'category_name' => 'test',
                'date' => $date,
            ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => $amount,
            'type' => 'entry',
            'description' => $description,
            'category_name' => 'test',
            'date' => $date,
        ]);
    }

    public function test_user_can_delete_transaction()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $transaciton = Transaction::factory()->create();

        $this->deleteJson(route('transaction-destroy', $transaciton->id))
            ->assertOk()
            ->assertJsonStructure([
                'message',
            ])
            ->assertJsonFragment([
                'message' => 'transaction successfully deleted',
            ]);

        $this->assertDatabaseEmpty(Transaction::class);
    }

    public function test_user_can_query_transactions_by_date()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $otherUserTransaction = Transaction::factory()->create([
            'date' => '2025-12-03',
        ]);

        $userTransaction = Transaction::factory()->create([
            'date' => '2025-03-13',
        ]);

        $this->getJson(route('transaction-query-date', ['2025-03-01', '2025-03-30']))
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'transactions',
            ])
            ->assertJsonFragment([
                'date' => $userTransaction->date,
            ])
            ->assertJsonMissing([
                'date' => $otherUserTransaction->date,
            ]);

        $this->assertDatabaseCount(Transaction::class, 2);
    }

    public function test_user_can_query_transactions_by_type()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $otherUserTransaction = Transaction::factory()->create([
            'type' => 'withdraw',
        ]);

        $userTransaction = Transaction::factory()->create([
            'type' => 'entry',
        ]);

        $this->getJson(route('transaction-query-type', 'entry'))
            ->assertOK()
            ->assertJsonStructure([
                'message',
                'transactions',
            ])
            ->assertJsonFragment([
                'type' => $userTransaction->type,
            ])
            ->assertJsonMissing([
                'type' => $otherUserTransaction->type,
            ]);

        $this->assertDatabaseCount(Transaction::class, 2);
    }

    public function test_user_can_query_transactions_by_category()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        Category::factory()->create([
            'name' => 'User Transaction',
            'isPersonalizada' => true,
        ]);

        $userTransaction = Transaction::factory()->create([
            'category_name' => 'User Transaction',
        ]);

        $othterUserTransaction = Transaction::factory()->create([
            'category_name' => 'Other User Transaction',
        ]);

        $this->assertDatabaseCount(Category::class, 5);

        $this->assertDatabaseCount(Transaction::class, 2);

        $this->getJson(route('transaction-query-category', $userTransaction->category_name))
            ->assertOK()
            ->assertJsonStructure([
                'message',
                'transactions',
            ])
            ->assertJsonFragment([
                'category_name' => $userTransaction->category_name,
            ])
            ->assertJsonMissing([
                'category_name' => $othterUserTransaction->category_name,
            ]);
    }
}
