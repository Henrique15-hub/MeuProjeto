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

    public function test_user_can_create_entry_transaction(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $transaction = [
            'wallet_id' => 1,
            'amount' => 1550,
            'type' => 'entry',
            'description' => 'test_user_can_create_entry_transaction',
            'category_name' => 'test',
            'date' => '2025-01-01',
        ];

        $this->postJson(route('transaction-entry'), $transaction)
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => 1,
            'amount' => 1550,
            'type' => 'entry',
            'description' => 'test_user_can_create_entry_transaction',
            'category_name' => 'Test',
            'date' => '2025-01-01',

        ]);
    }

    public function test_user_can_create_withdraw_transaction()
    {
        $user = User::factory()->create();
        $transaction = [
            'wallet_id' => 1,
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

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => 1,
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

        Transaction::factory()->create();
        $id = 1;
        $transaction = [
            'amount' => 2000,
            'type' => 'entry',
            'description' => 'test_user_can_update_transction',
            'category_name' => 'test',
            'date' => '2025-01-03',
        ];

        $this->putJson('api/transaction/update/1', $transaction)
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'transaction',
            ])
            ->assertJsonFragment([
                'amount' => 2000,
                'type' => 'entry',
                'description' => 'test_user_can_update_transction',
                'category_name' => 'test',
                'date' => '2025-01-03',
            ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => 2000,
            'type' => 'entry',
            'description' => 'test_user_can_update_transction',
            'category_name' => 'test',
            'date' => '2025-01-03',
        ]);
    }

    public function test_user_can_delete_transaction()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        Transaction::factory()->create();

        $this->deleteJson('api/transaction/destroy/1')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ])
            ->assertJsonFragment([
                'message' => 'transaction successfully deleted',
            ]);

        $this->assertDatabaseEmpty('transactions');
    }

    public function test_user_can_query_transactions_by_date()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        Transaction::factory()->count(3)->create([
            'date' => '2025-12-03',
        ]);

        Transaction::factory()->create([
            'date' => '2025-03-13',
        ]);

        $this->getJson('api/transaction/queryDate/2025-03-05/2025-03-30')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'transactions',
            ])
            ->assertJsonFragment([
                'date' => '2025-03-13',
            ]);

        $this->assertDatabaseCount('transactions', 4);
    }

    public function test_user_can_query_transactions_by_type()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        Transaction::factory()->count(3)->create([
            'type' => 'withdraw',
        ]);

        Transaction::factory()->create([
            'type' => 'entry',
        ]);

        $this->getJson('api/transaction/queryType/entry')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'transactions',
            ])
            ->assertJsonFragment([
                'type' => 'entry',
            ])
            ->assertJsonMissing([
                'type' => 'withdraw',
            ]);

        $this->assertDatabaseCount('transactions', 4);
    }

    public function test_user_can_query_transactions_by_category()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        Category::create([
            'name' => 'Test',
            'isPersonalizada' => true,
            'user_id' => auth()->id(),
        ]);

        Transaction::factory()->count(5)->create([
            'category_name' => 'Test',
        ]);

        Transaction::factory()->create([
            'category_name' => 'Test2',
        ]);

        $this->assertDatabaseCount('categories', 5);

        $this->assertDatabaseCount('transactions', 6);

        $this->getJson('api/transaction/queryCategory/Test')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'transactions',
            ])
            ->assertJsonFragment([
                'category_name' => 'Test',
            ])
            ->assertJsonMissing([
                'category_name' => 'Test2',
            ]);
    }
}
