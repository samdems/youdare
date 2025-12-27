<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post("register", [AuthController::class, "register"]);
Route::post("login", [AuthController::class, "login"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post("logout", [AuthController::class, "logout"]);
    Route::get("me", [AuthController::class, "me"]);
});

// Task API routes - require admin
Route::prefix("tasks")->group(function () {
    Route::middleware("admin")->group(function () {
        Route::get("statistics", [TaskController::class, "statistics"]);
        Route::get("/", [TaskController::class, "index"]);
        Route::get("{task}", [TaskController::class, "show"]);
        Route::post("/", [TaskController::class, "store"]);
        Route::put("{task}", [TaskController::class, "update"]);
        Route::patch("{task}", [TaskController::class, "update"]);
        Route::delete("{task}", [TaskController::class, "destroy"]);
        Route::patch("bulk", [TaskController::class, "bulkUpdate"]);
        Route::delete("bulk", [TaskController::class, "bulkDelete"]);
        Route::patch("{task}/toggle-draft", [
            TaskController::class,
            "toggleDraft",
        ]);
    });
});

// Tag API routes
Route::prefix("tags")->group(function () {
    // Public routes
    Route::get("/", [TagController::class, "index"]);
    Route::get("{tag}", [TagController::class, "show"]);

    // Admin-only routes
    Route::middleware("admin")->group(function () {
        Route::post("/", [TagController::class, "store"]);
        Route::put("{tag}", [TagController::class, "update"]);
        Route::patch("{tag}", [TagController::class, "update"]);
        Route::delete("{tag}", [TagController::class, "destroy"]);
    });
});

// Game API routes
Route::prefix("games")->group(function () {
    // Find game by code
    Route::post("find", [GameController::class, "findByCode"]);

    // Standard CRUD operations
    Route::get("/", [GameController::class, "index"]);
    Route::post("/", [GameController::class, "store"]);
    Route::get("{game}", [GameController::class, "show"]);
    Route::put("{game}", [GameController::class, "update"]);
    Route::patch("{game}", [GameController::class, "update"]);
    Route::delete("{game}", [GameController::class, "destroy"]);

    // Game actions
    Route::post("{game}/start", [GameController::class, "start"]);
    Route::post("{game}/complete", [GameController::class, "complete"]);

    // Game tag management
    Route::post("{game}/tags/sync", [GameController::class, "syncTags"]);
    Route::post("{game}/tags/{tag}/attach", [
        GameController::class,
        "attachTag",
    ]);
    Route::delete("{game}/tags/{tag}/detach", [
        GameController::class,
        "detachTag",
    ]);

    // Get available tasks for game
    Route::get("{game}/tasks", [GameController::class, "availableTasks"]);

    // Player routes nested under games
    Route::prefix("{game}/players")->group(function () {
        Route::get("/", [PlayerController::class, "index"]);
        Route::post("/", [PlayerController::class, "store"]);
    });
});

// Player API routes
Route::prefix("players")->group(function () {
    // Standard CRUD operations
    Route::get("{player}", [PlayerController::class, "show"]);
    Route::put("{player}", [PlayerController::class, "update"]);
    Route::patch("{player}", [PlayerController::class, "update"]);
    Route::delete("{player}", [PlayerController::class, "destroy"]);

    // Player tag management
    Route::post("{player}/tags/sync", [PlayerController::class, "syncTags"]);
    Route::post("{player}/tags/{tag}/attach", [
        PlayerController::class,
        "attachTag",
    ]);
    Route::delete("{player}/tags/{tag}/detach", [
        PlayerController::class,
        "detachTag",
    ]);

    // Get tasks for player
    Route::get("{player}/tasks", [PlayerController::class, "availableTasks"]);
    Route::get("{player}/tasks/random", [
        PlayerController::class,
        "randomTask",
    ]);

    // Player actions
    Route::post("{player}/score", [PlayerController::class, "incrementScore"]);
    Route::post("{player}/complete-task", [
        PlayerController::class,
        "completeTask",
    ]);
});
