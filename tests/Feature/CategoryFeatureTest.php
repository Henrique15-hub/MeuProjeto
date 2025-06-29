<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_create_personalized_category(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $category = [
            'name' => 'Personalized Category',
            'isPersonalizada' => true,
        ];

        $this->postJson(route('category-store'), $category)
            ->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'category',
            ])
            ->assertJsonFragment([
                'name' => 'Personalized Category',
                'isPersonalizada' => true,
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Personalized Category',
            'isPersonalizada' => true,
            'user_id' => auth()->id(),
        ]);
    }

    public function test_user_can_see_only_his_categories_and_defauts()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        Category::factory()->create();

        Category::create([
            'name' => 'Other User Category',
            'isPersonalizada' => true,
            'user_id' => 10,
        ]);

        $this->getJson(route('category-index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'categories',
            ])
            ->assertJsonFragment([
                'user_id' => 0,
            ])
            ->assertJsonMissing([
                'name' => 'Other User Category',
            ]);
        $this->assertDatabaseCount('categories', 6);
    }

    public function test_user_can_update_category()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        Category::factory()->create();

        $cateogry = [
            'name' => 'Updated category',
        ];

        $this->putJson('api/category/update/1', $cateogry)
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'category',
            ])
            ->assertJsonFragment([
                'name' => 'Updated category',
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Updated category',
        ]);
    }
}
