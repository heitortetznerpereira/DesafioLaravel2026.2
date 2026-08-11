<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function create()
    {
        return view("users.create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "string", "email", "max:255", "unique:users"],
            "password" => ["required", "string", "min:8"],
            "is_admin" => ["required", "boolean"],
            "cpf" => ["required", "string", "max:14", "unique:users"],
            "phone_number" => ["required", "string", "max:15"],
            "birth_date" => ["required", "date"],
            "image" => ["nullable", "image", "max:2048"],
            "balance" => ["required", "numeric", "min:0"],
            "cep" => ["required", "string", "max:9"],
            "street" => ["required", "string", "max:255"],
            "number" => ["required", "string", "max:10"],
            "complement" => ["nullable", "string", "max:255"],
            "neighborhood" => ["required", "string", "max:255"],
            "city" => ["required", "string", "max:255"],
            "state" => ["required", "string", "max:2"],
        ]);

        $imagePath = $request->file("image")->store("users", "public");

        DB::transaction(function () use ($validated) {
            $user = User::create([
                "name" => $validated["name"],
                "email" => $validated["email"],
                "password" => bcrypt($validated["password"]),
                "is_admin" => $validated["is_admin"],
                "cpf" => $validated["cpf"],
                "phone_number" => $validated["phone_number"],
                "birth_date" => $validated["birth_date"],
                "image_path" => $imagePath,
                "balance" => $validated["balance"],
            ]);

            $user->address()->create([
                "cep" => $validated["cep"],
                "street" => $validated["street"],
                "number" => $validated["number"],
                "complement" => $validated["complement"] ?? null,
                "neighborhood" => $validated["neighborhood"],
                "city" => $validated["city"],
                "state" => $validated["state"],
            ]);
        });

        return redirect()->route("users.index")->with("success", "Usuário criado com sucesso");
    }

    public function edit(User $user)
    {
        if ($user->id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        return view("users.edit", [
            "user" => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        if ($user->id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "string", "email", "max:255", "unique:users,email," . $user->id],
            "password" => ["nullable", "string", "min:8", "confirmed"],
            "is_admin" => ["nullable", "boolean"],
            "cpf" => ["required", "string", "max:14", "unique:users,cpf," . $user->id],
            "phone_number" => ["required", "string", "max:20"],
            "birth_date" => ["required", "date"],
            "image" => ["nullable", "image", "max:2048"],
            "balance" => ["required", "numeric", "min:0"],
            "cep" => ["required", "string", "max:9"],
            "street" => ["required", "string", "max:255"],
            "number" => ["required", "string", "max:10"],
            "complement" => ["nullable", "string", "max:255"],
            "neighborhood" => ["required", "string", "max:255"],
            "city" => ["required", "string", "max:255"],
            "state" => ["required", "string", "max:2"],
        ]);

        if ($request->hasFile("image")) {
            Storage::disk("public")->delete($user->photo_path);

            $validated["photo_path"] = $request
                ->file("image")
                ->store("products", "public");
        }

        DB::transaction(function () use ($validated) {
            $user->update([
                "name" => $validated["name"],
                "email" => $validated["email"],
                "password" => isset($validated["password"]) ? bcrypt($validated["password"]) : $user->password,
                "is_admin" => $validated["is_admin"] ?? $user->is_admin,
                "cpf" => $validated["cpf"],
                "phone_number" => $validated["phone_number"],
                "birth_date" => $validated["birth_date"],
                "image_path" => $validated["image_path"] ?? $user->image_path,
                "balance" => $validated["balance"],
            ]);

            $user->address()->update([
                "cep" => $validated["cep"],
                "street" => $validated["street"],
                "number" => $validated["number"],
                "complement" => $validated["complement"] ?? null,
                "neighborhood" => $validated["neighborhood"],
                "city" => $validated["city"],
                "state" => $validated["state"],
            ]);
        });

        return redirect()
            ->route("products.index")
            ->with("success", "Produto atualizado com sucesso");
    }

    public function destroy(User $user)
    {
        if ($user->creator_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        Storage::disk("public")->delete($user->photo_path);

        DB::transaction(function () use ($user) {
            $user->address()->delete();
            $user->delete();
        });

        return redirect()
            ->route("users.index")
            ->with("success", "Usuário excluído com sucesso");
    }
}
