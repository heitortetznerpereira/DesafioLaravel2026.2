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
        $product = Product::query()->exists()
            ? Product::inRandomOrder()->first()
            : Product::factory()->create();

        $buyer = User::query()->exists()
            ? User::inRandomOrder()->first()
            : User::factory()->create(['is_admin' => false]);

        $seller = User::query()->exists()
            ? User::inRandomOrder()->first()
            : User::factory()->create(['is_admin' => false]);

        return [
            "product_id" => $product->id,
            "buyer_id" => $buyer->id,
            "seller_id" => $seller->id,
            "status" => "pending",
            "unit_price" => $product->price,
            "name" => $product->name,
            "image" => $product->image,
            "amount" => fake()->numberBetween(1, 5),
        ];
    }
}
