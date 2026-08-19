<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminMailController;
use App\Http\Controllers\PagBankController;
use App\Http\Controllers\ProductsOnCartController;
use App\Http\Controllers\UserController;

Route::get("/welcome", function () {
    return view("welcome");
})->name("welcome");

Route::get("/dashboard", function () {
    return view("dashboard");
})
    ->middleware(["auth", "verified"])
    ->name("dashboard");

Route::post("/pagbank/webhook", [PagBankController::class, "webhook"])->name(
    "pagbank.webhook",
);

Route::get("/", [HomeController::class, "index"])->name("home");

Route::middleware("auth")->group(function () {
    Route::get("/profile", [ProfileController::class, "edit"])->name(
        "profile.edit",
    );
    Route::patch("/profile", [ProfileController::class, "update"])->name(
        "profile.update",
    );
    Route::delete("/profile", [ProfileController::class, "destroy"])->name(
        "profile.destroy",
    );

    /*
    Route::get("/individual_product/{product}", [
        ProductController::class,
        "show",
    ])->name("product.show");
    */

    Route::resource("products", ProductController::class);

    Route::get("/sales", [SaleController::class, "index"])->name("sales.index");

    Route::get("/sales/pdf", [SaleController::class, "exportPdf"])->name(
        "sales.pdf",
    );


    Route::get("/sales/xlsx", [SaleController::class, "exportExcel"])->name(
        "sales.xlsx",
    );


    Route::get("/purchases", [SaleController::class, "purchases"])->name("purchases.index");


    Route::get("/purchases/pdf", [SaleController::class, "exportPdfPurchase"])->name(
        "purchases.pdf",
    );

    Route::get("/admin/mail", [AdminMailController::class, "create"])->name(
        "admin.mail.create",
    );

    Route::post("/admin/mail", [AdminMailController::class, "store"])->name(
        "admin.mail.store",
    );

    Route::get("/users", [UserController::class, "index"])->name("users.index");
    Route::get("/users/create", [UserController::class, "create"])->name(
        "users.create",
    );
    Route::post("/users/store", [UserController::class, "store"])->name(
        "users.store",
    );
    Route::get("/users/edit/{user}", [UserController::class, "edit"])->name(
        "users.edit",
    );
    Route::put("/users/update/{user}", [UserController::class, "update"])->name(
        "users.update",
    );
    Route::get("/users/show/{user}", [UserController::class, "show"])->name(
        "users.show",
    );
    Route::delete("/users/delete/{user}", [
        UserController::class,
        "destroy",
    ])->name("users.destroy");

    Route::get("/admins", [UserController::class, "admins"])->name(
        "admins.index",
    );
    Route::get("/admins/show/{user}", [UserController::class, "show"])->name(
        "admins.show",
    );
    Route::get("/admins/create", [UserController::class, "createAdmin"])->name(
        "admins.create",
    );
    Route::post("/admins/store", [UserController::class, "store"])->name(
        "admins.store",
    );
    Route::get("/admins/edit/{user}", [UserController::class, "edit"])->name(
        "admins.edit",
    );
    Route::put("/admins/update/{user}", [
        UserController::class,
        "update",
    ])->name("admins.update");
    Route::delete("/admins/delete/{user}", [
        UserController::class,
        "destroy",
    ])->name("admins.destroy");


    Route::post("/products/{product}/buy", [
        SaleController::class,
        "buy",
    ])->name("products.buy");

    Route::get("/sales/{sale}/return", [SaleController::class, "return"])->name(
        "sales.return",
    );

    Route::get("/cart", [ProductsOnCartController::class, "index"])->name("cart.index");
    Route::post("/cart/store", [ProductsOnCartController::class, "store"])->name("cart.store");
    Route::delete("/cart/{cartProduct}", [ProductsOnCartController::class, "destroy"])->name("cartProducts.destroy");
    Route::post("/cart/close", [ProductsOnCartController::class, "close"])->name("cart.close");
});

require __DIR__ . "/auth.php";
