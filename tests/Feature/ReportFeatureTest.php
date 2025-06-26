<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_make_total_report()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        Transaction::factory()->count(5)->create([
            'type' => 'withdraw',
            'amount' => 1500,
        ]);

        $this->getJson('api/report/total')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'balance',
            ]);

        $this->assertDatabaseHas('reports', [
            'user_id' => auth()->id(),
            'date' => 'all',
            'value' => -7500,
            'type' => 'total',
        ]);

    }

    public function test_user_can_make_a_mensal_report()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        Transaction::factory()->count(3)->create([
            'date' => '2025-01-03',
            'type' => 'entry',
            'amount' => 3000,
        ]);

        $this->getJson('api/report/month/2025/01')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'balance',
            ]);

        $this->assertDatabaseHas('reports', [
            'user_id' => auth()->id(),
            'date' => '2025-01',
            'value' => 9000,
            'type' => 'month',
        ]);
    }

    public function test_user_can_make_a_personalized_report()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        Transaction::factory()->count(3)->create([
            'date' => '2025-03-13',
            'type' => 'entry',
            'amount' => 2500,
        ]);

        $this->getJson('api/report/personalized/2025-03-05/2025-03-30')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'balance',
            ]);

        $this->assertDatabaseHas('reports', [
            'user_id' => auth()->id(),
            'date' => '2025-03-05==>2025-03-30',
            'value' => 7500,
            'type' => 'personalized',
        ]);
    }
}
