<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_account_with_wallet(): void
    {
        $user = [
            'name' => 'Teste',
            'email' => 'teste@mail.com',
            'password' => 'teste123',
        ];

        $this->postJson(route('user-store'), $user)
            ->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user',
                'wallet',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Teste',
            'email' => 'teste@mail.com',
        ]);

        $this->assertDatabaseHas('wallets', [
            'user_id' => 1,
            'balance' => 0,
        ]);
    }

    public function test_user_can_delete_account()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $this->deleteJson(route('user-destroy'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ]);

        $this->assertEquals(0, User::count());
    }
}
