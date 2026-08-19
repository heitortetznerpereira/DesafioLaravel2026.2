<?php

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\Http;

it('displays a cart button instead of direct buy on the product page', function () {
    $seller = User::factory()->create(['is_admin' => false]);
    $buyer = User::factory()->create(['is_admin' => false]);
    $product = Product::factory()->create([
        'creator_id' => $seller->id,
    ]);

    $response = $this
        ->actingAs($buyer)
        ->get(route('products.show', $product));

    $response
        ->assertOk()
        ->assertSee('Adicionar ao Carrinho')
        ->assertDontSee('Comprar');
});

it('adds a product to the cart for the authenticated user', function () {
    $seller = User::factory()->create(['is_admin' => false]);
    $buyer = User::factory()->create(['is_admin' => false]);
    $product = Product::factory()->create([
        'creator_id' => $seller->id,
        'amount' => 10,
    ]);

    $response = $this
        ->actingAs($buyer)
        ->post(route('cart.store'), [
            'product_id' => $product->id,
            'amount' => 3,
        ]);

    $response
        ->assertRedirect(route('cart.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('products_on_cart', [
        'user_id' => $buyer->id,
        'product_id' => $product->id,
        'amount' => 3,
    ]);
});

it('blocks adding more items than the available stock', function () {
    $seller = User::factory()->create(['is_admin' => false]);
    $buyer = User::factory()->create(['is_admin' => false]);
    $product = Product::factory()->create([
        'creator_id' => $seller->id,
        'amount' => 2,
    ]);

    $response = $this
        ->actingAs($buyer)
        ->from(route('home'))
        ->post(route('cart.store'), [
            'product_id' => $product->id,
            'amount' => 3,
        ]);

    $response
        ->assertRedirect(route('cart.index'))
        ->assertSessionHasErrors('product');

    $this->assertDatabaseCount('products_on_cart', 0);
});

it('blocks the owner from adding their own product to the cart', function () {
    $seller = User::factory()->create(['is_admin' => false]);
    $product = Product::factory()->create([
        'creator_id' => $seller->id,
    ]);

    $response = $this
        ->actingAs($seller)
        ->post(route('cart.store'), [
            'product_id' => $product->id,
            'amount' => 1,
        ]);

    $response
        ->assertRedirect(route('cart.index'))
        ->assertSessionHasErrors('product');

    $this->assertDatabaseCount('products_on_cart', 0);
});

it('removes an item from the cart', function () {
    $seller = User::factory()->create(['is_admin' => false]);
    $buyer = User::factory()->create(['is_admin' => false]);
    $product = Product::factory()->create([
        'creator_id' => $seller->id,
    ]);

    $this->actingAs($buyer)
        ->post(route('cart.store'), [
            'product_id' => $product->id,
            'amount' => 2,
        ]);

    $cartItem = \App\Models\ProductsOnCart::first();

    $response = $this->actingAs($buyer)->delete(route('cartProducts.destroy', ['cartProduct' => $cartItem->id]));

    $response->assertRedirect(route('cart.index'));
    $this->assertDatabaseMissing('products_on_cart', [
        'id' => $cartItem->id,
    ]);
});

it('closes the cart and creates sales for each item', function () {
    $seller = User::factory()->create(['is_admin' => false]);
    $buyer = User::factory()->create(['is_admin' => false]);

    $firstProduct = Product::factory()->create(['creator_id' => $seller->id, 'amount' => 10]);
    $secondProduct = Product::factory()->create(['creator_id' => $seller->id, 'amount' => 10]);

    $this->actingAs($buyer)
        ->post(route('cart.store'), ['product_id' => $firstProduct->id, 'amount' => 1]);

    $this->actingAs($buyer)
        ->post(route('cart.store'), ['product_id' => $secondProduct->id, 'amount' => 2]);

    Http::fake([
        'https://sandbox.api.pagseguro.com/checkouts' => Http::response([
            'id' => 'checkout_123',
            'links' => [[
                'rel' => 'PAY',
                'href' => 'https://example.com/pay',
            ]],
        ], 201),
    ]);

    $response = $this->actingAs($buyer)->post(route('cart.close'));

    $response->assertRedirect('https://example.com/pay');
    $this->assertDatabaseHas('sales', [
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'product_id' => $firstProduct->id,
    ]);
    $this->assertDatabaseHas('sales', [
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'product_id' => $secondProduct->id,
        'amount' => 2,
    ]);
    $this->assertDatabaseCount('products_on_cart', 0);
});

it('sends a valid pagseguro checkout payload with absolute callback urls', function () {
    $seller = User::factory()->create(['is_admin' => false]);
    $buyer = User::factory()->create(['is_admin' => false]);
    $product = Product::factory()->create([
        'creator_id' => $seller->id,
        'name' => 'Notebook Gamer',
        'price' => 1999.00,
    ]);

    Http::fake([
        'https://sandbox.api.pagseguro.com/checkouts' => Http::response([
            'id' => 'checkout_123',
            'links' => [[
                'rel' => 'PAY',
                'href' => 'https://example.com/pay',
            ]],
        ], 201),
    ]);

    $response = $this
        ->actingAs($buyer)
        ->post(route('products.buy', $product), [
            'amount' => 1,
        ]);

    $response->assertRedirect('https://example.com/pay');

    Http::assertSent(function ($request) use ($product) {
        return $request->url() === 'https://sandbox.api.pagseguro.com/checkouts'
            && $request->hasHeader('Authorization', 'Bearer ' . config('services.pagbank.token'))
            && $request->hasHeader('Content-Type', 'application/json')
            && $request['reference_id'] === 'sale-' . Sale::latest()->first()->id
            && $request['items'][0]['name'] === $product->name
            && $request['redirect_url'] === route('sales.return', Sale::latest()->first(), true)
            && $request['payment_notification_urls'][0] === url('/pagbank/webhook');
    });
});
