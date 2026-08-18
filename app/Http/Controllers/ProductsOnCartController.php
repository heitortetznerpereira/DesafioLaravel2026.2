<?php

namespace App\Http\Controllers;

use App\Models\ProductsOnCart;
use Illuminate\Http\Request;

class ProductsOnCartController extends Controller
{
    //
    public function index() {

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "user_id" => ["required", "exists:users,id"],
            "product_id" => ["required", "exists:products,id"],
            "amount" => ["integer", "min:0"],
        ]);

        ProductsOnCart::create([
            "user_id" => $validated["user_id"],
            "product_id" => $validated["product_id"],
            "amount" => $validated["amount"],
        ]);

        return redirect()
            ->route("cart.index")
            ->with("success", "Produto adicionado com sucesso");
    }
}
