<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query();

        if ($request->filled("search")) {
            $products->where(
                "name",
                "like",
                "%" . $request->input("search") . "%",
            );
        }

        if ($request->filled("category")) {
            $products->where("category_id", $request->category);
        }

        $products = $products->paginate(9)->withQueryString();

        $categories = Category::orderBy("name")->get();

        return view("home", [
            "products" => $products,
            "categories" => $categories,
        ]);
    }
}
