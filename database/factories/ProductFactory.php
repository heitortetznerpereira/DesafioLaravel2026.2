<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 5, 500),
            'image' => fake()->imageUrl(),
            'amount' => fake()->numberBetween(1, 100),
            'category_id' => Category::inRandomOrder()->first()->id,
            'creator_id' => User::inRandomOrder()->first()->id,

        ];
    }
}
