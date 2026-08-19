<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        return view("products.individual_product", [
            "product" => $product,
        ]);
    }

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

        $categories = Category::orderBy("name")->get();

        if (!Auth::user()->is_admin) {
                    $products = $products->where("creator_id", Auth::id())->paginate(10);
                } else {
                    $products = $products->paginate(10);

                    $monthExpression = DB::connection()->getDriverName() === "sqlite"
                        ? "strftime('%Y-%m', created_at)"
                        : "DATE_FORMAT(created_at, '%Y-%m')";

                    $productChart = Product::query()
                        ->selectRaw("$monthExpression as month, COUNT(*) as total")
                        ->where("created_at", ">=", now()->subMonths(11)->startOfMonth())
                        ->groupBy("month")
                        ->orderBy("month")
                        ->get()
                        ->keyBy("month");

                    $productChartLabels = [];
                    $productChartData = [];

                    for ($i = 11; $i >= 0; $i--) {
                        $month = now()->subMonths($i)->startOfMonth();
                        $key = $month->format("Y-m");

                        $productChartLabels[] = $month->translatedFormat("M");
                        $productChartData[] = $productChart->get($key)?->total ?? 0;
                    }
                }

        return view("products.index", [
            "products" => $products,
            "categories" => $categories,
            "productChartLabels" => $productChartLabels ?? [],
            "productChartData" => $productChartData ?? [],
        ]);
    }

    public function create()
    {
        if (Auth::user()->is_admin) {
            abort(403);
        }
        return view("products.create", [
            "categories" => Category::all(),
        ]);
    }

    public function store(Request $request)
    {
        if (Auth::user()->is_admin) {
            abort(403);
        }
        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "price" => ["required", "numeric", "min:0"],
            "amount" => ["required", "integer", "min:0"],
            "description" => ["required", "string"],
            "category_id" => ["required", "exists:categories,id"],
            "image" => ["required", "image", "max:2048"],
        ]);

        $image = $request->file("image")->store("products", "public");

        Product::create([
            "name" => $validated["name"],
            "price" => $validated["price"],
            "amount" => $validated["amount"],
            "description" => $validated["description"],
            "category_id" => $validated["category_id"],
            "creator_id" => Auth::id(),
            "image" => $image,
        ]);

        return redirect()
            ->route("products.index")
            ->with("success", "Produto criado com sucesso");
    }

    public function edit(Product $product)
    {
        if ($product->creator_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        return view("products.edit", [
            "product" => $product,
            "categories" => Category::all(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        if ($product->creator_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "price" => ["required", "numeric", "min:0"],
            "amount" => ["required", "integer", "min:0"],
            "description" => ["required", "string"],
            "category_id" => ["required", "exists:categories,id"],
            "image" => ["nullable", "image", "max:2048"],
        ]);

        if ($request->hasFile("image")) {
            Storage::disk("public")->delete($product->image);

            $validated["image"] = $request
                ->file("image")
                ->store("products", "public");
        }

        $product->update([
            "name" => $validated["name"],
            "price" => $validated["price"],
            "amount" => $validated["amount"],
            "description" => $validated["description"],
            "category_id" => $validated["category_id"],
            "image" => $validated["image"] ?? $product->image,
        ]);

        return redirect()
            ->route("products.index")
            ->with("success", "Produto atualizado com sucesso");
    }

    public function destroy(Product $product)
    {
        if ($product->creator_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        Storage::disk("public")->delete($product->image);

        $product->delete();

        return redirect()
            ->route("products.index")
            ->with("success", "Produto excluído com sucesso");
    }
}
