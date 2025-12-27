<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

// Authentication routes
Route::middleware("guest")->group(function () {
    Route::get("register", [AuthController::class, "showRegisterForm"])->name(
        "register",
    );
    Route::post("register", [AuthController::class, "register"]);
    Route::get("login", [AuthController::class, "showLoginForm"])->name(
        "login",
    );
    Route::post("login", [AuthController::class, "login"]);
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

// Stats route - require admin
Route::middleware("admin")->group(function () {
    Route::get("stats", [StatsController::class, "index"])->name("stats.index");
});

// Task routes - specific routes first (before parameterized routes)
Route::get("tasks/random", [TaskController::class, "random"])->name(
    "tasks.random",
);

// Task routes - require admin (specific routes)
Route::middleware("admin")->group(function () {
    Route::get("tasks/create", [TaskController::class, "create"])->name(
        "tasks.create",
    );
});

// Task routes - public viewing
Route::get("tasks", [TaskController::class, "index"])->name("tasks.index");
Route::get("tasks/{task}", [TaskController::class, "show"])->name("tasks.show");

// Task routes - require admin (parameterized routes)
Route::middleware("admin")->group(function () {
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

// Tag routes - require admin (specific routes first)
Route::middleware("admin")->group(function () {
    Route::get("tags/create", [TagController::class, "create"])->name(
        "tags.create",
    );
});

// Tag routes - public viewing
Route::get("tags", [TagController::class, "index"])->name("tags.index");
Route::get("tags/{tag}", [TagController::class, "show"])->name("tags.show");

// Tag routes - require admin (parameterized routes)
Route::middleware("admin")->group(function () {
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
