<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminMailController;
use App\Http\Controllers\UserController;

Route::get("/welcome", function () {
    return view("welcome");
})->name("welcome");

Route::get("/dashboard", function () {
    return view("dashboard");
})
    ->middleware(["auth", "verified"])
    ->name("dashboard");

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

    Route::get("/", [HomeController::class, "index"])->name("home");

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

    Route::get("/admin/mail", [AdminMailController::class, "create"])->name(
        "admin.mail.create",
    );

    Route::post("/admin/mail", [AdminMailController::class, "store"])->name(
        "admin.mail.store",
    );

    Route::resource("/users", UserController::class);
});

require __DIR__ . "/auth.php";
