<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductsOnCart;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        foreach ($cartProducts as $cartProduct) {
            $product = $cartProduct->product;

            if (!$product) {
                continue;
            }

            if ($product->creator_id === Auth::id()) {
                $cartProduct->delete();
                continue;
            }

            if ($product->amount < $cartProduct->amount) {
                $cartProduct->delete();
                continue;
            }

            $product->decrement("amount", $cartProduct->amount);

            Sale::create([
                "product_id" => $product->id,
                "buyer_id" => Auth::id(),
                "seller_id" => $product->creator_id,
                "status" => "pending",
                "image" => $product->image,
                "name" => $product->name,
                "amount" => $cartProduct->amount,
                "unit_price" => $product->price,
            ]);
        }

        ProductsOnCart::where("user_id", Auth::id())->delete();

        return redirect()
            ->route("cart.index")
            ->with("success", "Carrinho fechado com sucesso.");
    }
}
