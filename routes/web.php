<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PromoCodeController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

// Legal pages
Route::get("/privacy", function () {
    return view("privacy");
})->name("privacy");

Route::get("/terms", function () {
    return view("terms");
})->name("terms");

// Authentication routes
Route::middleware("guest")->group(function () {
    // Redirect old register routes to login
    Route::get("register", function () {
        return redirect()->route("login");
    })->name("register");

    Route::get("login", [AuthController::class, "showLoginForm"])->name(
        "login",
    );
    Route::post("login", [AuthController::class, "sendMagicLink"]);
    Route::get("magic-link/{token}", [
        AuthController::class,
        "verifyMagicLink",
    ])->name("magic-link.verify");
});

Route::post("logout", [AuthController::class, "logout"])
    ->middleware("auth")
    ->name("logout");

// Game route
Route::get("/game", function () {
    return view("game");
})->name("game");

// Test API route
Route::get("/test-api", function () {
    return view("test-api");
});

// Stripe/Pro routes
Route::middleware("auth")->group(function () {
    Route::get("go-pro", [StripeController::class, "showGoPro"])->name(
        "stripe.go-pro",
    );
    Route::post("checkout", [
        StripeController::class,
        "createCheckoutSession",
    ])->name("stripe.checkout");
    Route::get("payment/success", [StripeController::class, "success"])->name(
        "stripe.success",
    );
    Route::get("payment/cancel", [StripeController::class, "cancel"])->name(
        "stripe.cancel",
    );
    // API route for validating promo codes
    Route::post("api/validate-promo", [
        StripeController::class,
        "validatePromoCode",
    ]);
});

// Stats route - require admin
Route::middleware("admin")->group(function () {
    Route::get("stats", [StatsController::class, "index"])->name("stats.index");
});

// Task routes - require admin
Route::middleware("admin")->group(function () {
    Route::get("tasks/create", [TaskController::class, "create"])->name(
        "tasks.create",
    );
    Route::get("tasks", [TaskController::class, "index"])->name("tasks.index");
    Route::get("tasks/{task}", [TaskController::class, "show"])->name(
        "tasks.show",
    );
    Route::post("tasks", [TaskController::class, "store"])->name("tasks.store");
    Route::get("tasks/{task}/edit", [TaskController::class, "edit"])->name(
        "tasks.edit",
    );
    Route::put("tasks/{task}", [TaskController::class, "update"])->name(
        "tasks.update",
    );
    Route::delete("tasks/{task}", [TaskController::class, "destroy"])->name(
        "tasks.destroy",
    );
    Route::patch("tasks/{task}/toggle-draft", [
        TaskController::class,
        "toggleDraft",
    ])->name("tasks.toggleDraft");
});

// Tag routes - require admin
Route::middleware("admin")->group(function () {
    Route::get("tags/create", [TagController::class, "create"])->name(
        "tags.create",
    );
    Route::get("tags", [TagController::class, "index"])->name("tags.index");
    Route::get("tags/{tag}", [TagController::class, "show"])->name("tags.show");
    Route::post("tags", [TagController::class, "store"])->name("tags.store");
    Route::get("tags/{tag}/edit", [TagController::class, "edit"])->name(
        "tags.edit",
    );
    Route::put("tags/{tag}", [TagController::class, "update"])->name(
        "tags.update",
    );
    Route::delete("tags/{tag}", [TagController::class, "destroy"])->name(
        "tags.destroy",
    );
});

// Promo Code routes - require admin
Route::middleware("admin")->group(function () {
    Route::get("promo-codes", [PromoCodeController::class, "index"])->name(
        "promo-codes.index",
    );
    Route::get("promo-codes/create", [
        PromoCodeController::class,
        "create",
    ])->name("promo-codes.create");
    Route::post("promo-codes", [PromoCodeController::class, "store"])->name(
        "promo-codes.store",
    );
    Route::get("promo-codes/{promoCode}/edit", [
        PromoCodeController::class,
        "edit",
    ])->name("promo-codes.edit");
    Route::put("promo-codes/{promoCode}", [
        PromoCodeController::class,
        "update",
    ])->name("promo-codes.update");
    Route::delete("promo-codes/{promoCode}", [
        PromoCodeController::class,
        "destroy",
    ])->name("promo-codes.destroy");
});
