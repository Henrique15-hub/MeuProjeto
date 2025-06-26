<?php

namespace Tests\Feature;

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
}
