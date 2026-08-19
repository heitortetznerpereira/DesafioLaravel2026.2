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
        $categoryId = Category::query()->exists()
            ? Category::inRandomOrder()->first()->id
            : Category::factory()->create()->id;

        $creatorId = User::query()->exists()
            ? User::inRandomOrder()->first()->id
            : User::factory()->create()->id;

        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 5, 500),
            'image' => fake()->imageUrl(),
            'amount' => fake()->numberBetween(1, 100),
            'category_id' => $categoryId,
            'creator_id' => $creatorId,
        ];
    }
}
