<?php

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;

it('shows the monthly product chart for admins', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    Product::factory()->create([
        'creator_id' => $admin->id,
        'created_at' => now()->subMonth(),
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('products.index'));

    $response
        ->assertOk()
        ->assertSee('productChart')
        ->assertSee('Produtos cadastrados');
});

it('shows the monthly sales chart for the authenticated user', function () {
    $seller = User::factory()->create(['is_admin' => false]);
    $buyer = User::factory()->create(['is_admin' => false]);

    Sale::factory()->create([
        'seller_id' => $seller->id,
        'buyer_id' => $buyer->id,
        'created_at' => now()->subMonth(),
    ]);

    $response = $this
        ->actingAs($seller)
        ->get(route('sales.index'));

    $response
        ->assertOk()
        ->assertSee('salesChart')
        ->assertSee('Vendas realizadas');
});
