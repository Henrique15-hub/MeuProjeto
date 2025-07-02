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
                'user_id' => null,
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

        $oldCategory = Category::factory()->create();

        $name = [
            'name' => 'Updated category',
        ];

        $this->putJson("api/category/update/{$oldCategory->id}", $name)
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

    public function test_user_can_see_a_specific_category()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $category = Category::factory()->create([
            'name' => 'Teste show',
        ]);

        $this->assertDatabaseHas(Category::class, [
            'name' => 'Teste show',
        ]);

        $this->getJson(route('category-show', $category->id))
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'category',
            ])
            ->assertJsonFragment([
                'name' => 'Teste show',
            ]);
    }

    public function test_user_can_destroy_category()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $category = Category::factory()->create([
            'name' => 'Teste show',
        ]);

        $this->deleteJson(route('category-destroy', $category->id))
            ->assertOk()
            ->assertJsonStructure([
                'message',
            ])
            ->assertJsonFragment([
                'message' => 'category deleted successfully',
            ]);
    }
}
