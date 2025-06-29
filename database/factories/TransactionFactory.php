<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wallet_id' => auth()->id(),
            'type' => fake()->randomElement(['entry', 'withdraw']),
            'amount' => fake()->numberBetween(0.1, 9999),
            'description' => fake()->text('100'),
            'category_name' => fake()->word(),
            'date' => fake()->date(),
        ];
    }
}
