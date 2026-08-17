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
        $p = Product::inRandomOrder()->first();
        $u = User::inRandomOrder()->first();
        $s = User::inRandomOrder()->first();
        return [
            "product_id" => $p->id,
            "buyer_id" => $u->id,
            "seller_id" => $s->id,
            "unit_price" => $p->price,
            "name" => $p->name,
            "image" => $p->image,
            "amount" => fake()->randomDigit()
        ];
    }
}
