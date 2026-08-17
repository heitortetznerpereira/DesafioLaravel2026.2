<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "product_id" => Product::inRandomOrder()->first()->id,
            "buyer_id" => User::inRandomOrder()->first()->id,
            "seller_id" => User::inRandomOrder()->first()->id,
            "unit_price" => fake()->randomFloat(2, 10, 1000),
        ];
    }
}
