<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductsOnCart;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ProductsOnCartController extends Controller
{
    //
    public function index(Request $request)
    {
        $cartProducts = ProductsOnCart::query();
        $cartProducts->where("user_id", Auth::user()->id);
        if ($request->filled("search")) {
            $cartProducts->whereHas("product", function ($query) use (
                $request,
            ) {
                $query->where(
                    "name",
                    "like",
                    "%" . $request->input("search") . "%",
                );
            });
        }
        $cartProducts = $cartProducts->paginate(10);

        $categories = Category::orderBy("name")->get();
        return view("products.cart", [
            "cartProducts" => $cartProducts,
            "categories" => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "product_id" => ["required", "exists:products,id"],
            "amount" => ["required", "integer", "min:1"],
        ]);

        $product = Product::findOrFail($validated["product_id"]);

        if ($product->creator_id === Auth::id()) {
            return redirect()
                ->route("cart.index")
                ->withErrors([
                    "product" => "Você não pode adicionar seu próprio produto ao carrinho.",
                ]);
        }

        $existingAmount = ProductsOnCart::where("user_id", Auth::id())
            ->where("product_id", $product->id)
            ->value("amount") ?? 0;

        if ($existingAmount + $validated["amount"] > $product->amount) {
            return redirect()
                ->route("cart.index")
                ->withErrors([
                    "product" => "Quantidade indisponível em estoque.",
                ]);
        }

        $cartItem = ProductsOnCart::firstOrNew([
            "user_id" => Auth::user()->id,
            "product_id" => $validated["product_id"],
        ]);

        $cartItem->amount = ($cartItem->exists ? $cartItem->amount : 0) + $validated["amount"];
        $cartItem->save();

        return redirect()
            ->route("cart.index")
            ->with("success", "Produto adicionado com sucesso");
    }

    public function destroy(ProductsOnCart $cartProduct)
    {
        if ($cartProduct->user_id !== Auth::id()) {
            abort(403);
        }

        $cartProduct->delete();

        return redirect()
            ->route("cart.index")
            ->with("success", "Produto removido do carrinho.");
    }

    public function close()
    {
        $cartProducts = ProductsOnCart::with("product")
            ->where("user_id", Auth::id())
            ->get();

        if ($cartProducts->isEmpty()) {
            return redirect()
                ->route("cart.index")
                ->with("error", "Seu carrinho está vazio.");
        }

        $sales = [];

        foreach ($cartProducts as $cartProduct) {
            $product = $cartProduct->product;

            if (!$product) {
                continue;
            }

            if ($product->creator_id === Auth::id()) {
                continue;
            }

            if ($product->amount < $cartProduct->amount) {
                continue;
            }

            $sale = Sale::create([
                "product_id" => $product->id,
                "buyer_id" => Auth::id(),
                "seller_id" => $product->creator_id,
                "status" => "pending",
                "image" => $product->image,
                "name" => $product->name,
                "amount" => $cartProduct->amount,
                "unit_price" => $product->price,
            ]);

            $sales[] = $sale;
        }

        if (empty($sales)) {
            return redirect()
                ->route("cart.index")
                ->with("error", "Não foi possível fechar o carrinho. Verifique os itens disponíveis.");
        }

        $firstPayLink = null;

        foreach ($sales as $sale) {
            $response = Http::withHeaders([
                "Authorization" => "Bearer " . config("services.pagbank.token"),
                "Content-Type" => "application/json",
                "Accept" => "application/json",
            ])->post(config("services.pagbank.url") . "/checkouts", [
                "reference_id" => "sale-" . $sale->id,
                "customer" => [
                    "email" => Auth::user()->email,
                ],
                "items" => [[
                    "reference_id" => (string) $sale->id,
                    "name" => $sale->name,
                    "quantity" => $sale->amount,
                    "unit_amount" => (int) round($sale->unit_price * 100),
                ]],
                "charges" => [[
                    "description" => "Pagamento do produto " . $sale->name,
                    "amount" => [
                        "value" => (int) round($sale->unit_price * $sale->amount * 100),
                        "currency" => "BRL",
                    ],
                ]],
                "redirect_url" => route("sales.return", $sale, true),
                "return_url" => route("sales.return", $sale, true),
                "payment_notification_urls" => [
                    url("/pagbank/webhook"),
                ],
            ]);

            if ($response->failed()) {
                $sale->delete();
                continue;
            }

            $checkout = $response->json();
            $checkoutId = $checkout["id"] ?? null;

            if ($checkoutId) {
                $existsWithSameCheckout = Sale::where("pagbank_checkout_id", $checkoutId)
                    ->whereKeyNot($sale->id)
                    ->exists();

                if ($existsWithSameCheckout) {
                    $checkoutId = "checkout_" . $sale->id . "_" . now()->timestamp . "_" . random_int(1000, 9999);
                }
            }

            $sale->update([
                "pagbank_checkout_id" => $checkoutId,
            ]);

            $payLink = collect($checkout["links"] ?? [])->firstWhere("rel", "PAY");

            if ($payLink && !$firstPayLink) {
                $firstPayLink = $payLink["href"];
            }
        }

        ProductsOnCart::where("user_id", Auth::id())->delete();

        if (!$firstPayLink) {
            foreach ($sales as $sale) {
                if ($sale->exists) {
                    $sale->delete();
                }
            }

            return redirect()
                ->route("cart.index")
                ->with("error", "Não foi possível iniciar o pagamento no PagSeguro.");
        }

        return redirect()->away($firstPayLink);
    }
}
