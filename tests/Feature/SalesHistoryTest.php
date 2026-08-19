<?php

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;

it('shows the sales history with the product value for the seller', function () {
    $seller = User::factory()->create(['is_admin' => false]);
    $buyer = User::factory()->create(['is_admin' => false]);
    $product = Product::factory()->create([
        'creator_id' => $seller->id,
        'price' => 49.99,
    ]);

    Sale::factory()->create([
        'product_id' => $product->id,
        'seller_id' => $seller->id,
        'buyer_id' => $buyer->id,
        'name' => $product->name,
        'image' => $product->image,
        'unit_price' => 49.99,
        'amount' => 1,
    ]);

    $response = $this
        ->actingAs($seller)
        ->get(route('sales.index'));

    $response
        ->assertOk()
        ->assertSee($product->name)
        ->assertSee('R$ 49,99');
});
