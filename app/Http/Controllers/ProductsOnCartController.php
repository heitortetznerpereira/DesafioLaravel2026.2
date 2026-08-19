<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductsOnCart;
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
            "amount" => ["integer", "min:1"],
        ]);

        ProductsOnCart::create([
            "user_id" => Auth::user()->id,
            "product_id" => $validated["product_id"],
            "amount" => $validated["amount"],
        ]);

        return redirect()
            ->route("cart.index")
            ->with("success", "Produto adicionado com sucesso");
    }
}
