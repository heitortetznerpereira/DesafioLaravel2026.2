<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query();

        if ($request->filled("search")) {
            $users->where(
                "name",
                "like",
                "%" . $request->input("search") . "%",
            );
        }

        $users = $users->where("is_admin", false);

        if (!Auth::user()->is_admin) {
            $users = $users->where("id", Auth::id())->paginate(10);
        } else {
            $users = $users->paginate(10);
        }

        return view("users.index", [
            "users" => $users,
        ]);
    }

    public function admins(Request $request)
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        $users = User::query();

        if ($request->filled("search")) {
            $users->where(
                "name",
                "like",
                "%" . $request->input("search") . "%",
            );
        }

        $users = $users->where("is_admin", true)->paginate(10);

        return view("admins.index", [
            "users" => $users,
        ]);
    }

    public function show(User $user)
    {
        if ($user->id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        if ($user->is_admin) {
            return view("admins.show", [
                "user" => $user,
            ]);
        } else {
            return view("users.show", [
                "user" => $user,
            ]);
        }
    }

    public function create()
    {
        return view("users.create");
    }

    public function createAdmin()
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        return view("admins.create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                "unique:users",
            ],
            "password" => ["required", "string", "min:8"],
            "is_admin" => ["boolean"],
            "cpf" => ["required", "string", "max:14", "unique:users"],
            "phone_number" => ["required", "string", "max:15"],
            "birth_date" => ["required", "date"],
            "image" => ["nullable", "image", "max:2048"],
            "balance" => ["numeric", "min:0"],
            "cep" => ["required_if:is_admin,0", "string", "max:9"],
            "street" => ["required_if:is_admin,0", "string", "max:255"],
            "number" => ["required_if:is_admin,0", "string", "max:10"],
            "complement" => ["nullable", "string", "max:255"],
            "neighborhood" => ["required_if:is_admin,0", "string", "max:255"],
            "city" => ["required_if:is_admin,0", "string", "max:255"],
            "state" => ["required_if:is_admin,0", "string", "max:2"],
        ]);

        $image = null;

        if ($request->hasFile("image")) {
            $image = $request->file("image")->store("users", "public");
        }

        $creator = Auth::id();

        DB::transaction(function () use ($validated, $image, $creator) {
            $user = User::create([
                "name" => $validated["name"],
                "email" => $validated["email"],
                "password" => bcrypt($validated["password"]),
                "is_admin" => $validated["is_admin"] ?? false,
                "cpf" => $validated["cpf"],
                "phone_number" => $validated["phone_number"],
                "birth_date" => $validated["birth_date"],
                "image" => $image,
                "balance" => $validated["balance"] ?? 0,
                "creator_id" => $creator
            ]);

            if (!$user->is_admin) {
                $user->address()->create([
                    "cep" => $validated["cep"],
                    "street" => $validated["street"],
                    "number" => $validated["number"],
                    "complement" => $validated["complement"] ?? null,
                    "neighborhood" => $validated["neighborhood"],
                    "city" => $validated["city"],
                    "state" => $validated["state"],
                ]);
            }
        });

        $user = User::where("email", $validated["email"])->first();

        if ($user->is_admin) {
            return redirect()
                ->route("admins.index")
                ->with("success", "Administrador criado com sucesso");
        } else {
            return redirect()
                ->route("users.index")
                ->with("success", "Usuário criado com sucesso");
        }
    }

    public function edit(User $user)
    {
        if ($user->id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        if ($user->is_admin) {
            return view("admins.edit", [
                "user" => $user,
            ]);
        } else {
            return view("users.edit", [
                "user" => $user,
            ]);
        }
    }

    public function update(Request $request, User $user)
    {
        if ($user->id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                "unique:users,email," . $user->id,
            ],
            "password" => ["nullable", "string", "min:8", "confirmed"],
            "is_admin" => ["boolean"],
            "cpf" => [
                "required",
                "string",
                "max:14",
                "unique:users,cpf," . $user->id,
            ],
            "phone_number" => ["required", "string", "max:20"],
            "birth_date" => ["required", "date"],

            "image" => ["nullable", "image", "max:2048"],

            "balance" => ["numeric", "min:0"],
            "cep" => ["required_if:is_admin,0", "string", "max:8"],
            "street" => ["required_if:is_admin,0", "string", "max:255"],
            "number" => ["required_if:is_admin,0", "string", "max:10"],
            "complement" => ["nullable", "string", "max:255"],
            "neighborhood" => ["required_if:is_admin,0", "string", "max:255"],
            "city" => ["required_if:is_admin,0", "string", "max:255"],
            "state" => ["required_if:is_admin,0", "string", "max:255"],
        ]);

        if ($request->hasFile("image")) {
            $validated["image"] = $request
                ->file("image")
                ->store("users", "public");
            Storage::disk("public")->delete($user->image);
        }

        DB::transaction(function () use ($validated, $user) {
            $user->update([
                "name" => $validated["name"],
                "email" => $validated["email"],
                "password" => isset($validated["password"])
                    ? bcrypt($validated["password"])
                    : $user->password,
                "is_admin" => $validated["is_admin"] ?? $user->is_admin,
                "cpf" => $validated["cpf"],
                "phone_number" => $validated["phone_number"],
                "birth_date" => $validated["birth_date"],
                "image" => $validated["image"] ?? $user->image,
                "balance" => $validated["balance"] ?? $user->balance,
            ]);

            if (!$user->is_admin) {
                $user->address()->update([
                    "cep" => $validated["cep"],
                    "street" => $validated["street"],
                    "number" => $validated["number"],
                    "complement" => $validated["complement"] ?? null,
                    "neighborhood" => $validated["neighborhood"],
                    "city" => $validated["city"],
                    "state" => $validated["state"],
                ]);
            }
        });

        if ($user->is_admin) {
            return redirect()
                ->route("admins.index")
                ->with("success", "Administrador atualizado com sucesso");
        }

        return redirect()
            ->route("users.index")
            ->with("success", "Usuário atualizado com sucesso");
    }

    public function destroy(User $user)
    {
        $wasAdmin = $user->is_admin;
        if ($user->creator_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        Storage::disk("public")->delete($user->image);

        DB::transaction(function () use ($user) {
            $user->address()->delete();
            $user->delete();
        });

        if ($wasAdmin) {
            return redirect()
                ->route("admins.index")
                ->with("success", "Administrador excluído com sucesso");
        }

        return redirect()
            ->route("users.index")
            ->with("success", "Usuário excluído com sucesso");
    }
}
