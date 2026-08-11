<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function index()
    {
        if (!Auth::user()->is_admin) {
            $users = User::where("id", Auth::id())->paginate(10);
        } else {
            $users = User::paginate(10);
        }

        return view("users.index", [
            "users" => $users,
        ]);
    }

    /*
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

        $imagePath = $request->file("image")->store("products", "public");

        Product::create([
            "name" => $validated["name"],
            "price" => $validated["price"],
            "amount" => $validated["amount"],
            "description" => $validated["description"],
            "category_id" => $validated["category_id"],
            "creator_id" => Auth::id(),
            "image_path" => $imagePath,
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
            Storage::disk("public")->delete($product->image_path);

            $validated["image_path"] = $request
                ->file("image")
                ->store("products", "public");
        }

        $product->update([
            "name" => $validated["name"],
            "price" => $validated["price"],
            "amount" => $validated["amount"],
            "description" => $validated["description"],
            "category_id" => $validated["category_id"],
            "image_path" => $validated["image_path"] ?? $product->image_path,
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

        Storage::disk("public")->delete($product->image_path);

        $product->delete();

        return redirect()
            ->route("products.index")
            ->with("success", "Produto excluído com sucesso");
    }
    */
}
