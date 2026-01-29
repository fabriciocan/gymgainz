<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImpersonateController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas (requerem autenticação)
Route::middleware('auth:sanctum')->group(function () {
    // Autenticação
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Roles (lista para todos os usuários autenticados)
    Route::get('/roles', [RoleController::class, 'index']);

    // Impersonate status (para todos os usuários autenticados)
    Route::get('/impersonate/status', [ImpersonateController::class, 'status']);

    // Rotas administrativas
    Route::middleware('role:admin')->group(function () {
        // Usuários
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}/roles', [UserController::class, 'updateRoles']);
        Route::put('/users/{user}/toggle-active', [UserController::class, 'toggleActive']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::post('/users/mass-update-roles', [UserController::class, 'massUpdateRoles']);
        Route::get('/check-email', [UserController::class, 'checkEmail']);

        // Roles management
        Route::post('/roles', [RoleController::class, 'store']);
        Route::delete('/roles/{role}', [RoleController::class, 'destroy']);

        // Impersonate
        Route::post('/impersonate/{user}', [ImpersonateController::class, 'impersonate']);
        Route::post('/stop-impersonate', [ImpersonateController::class, 'stopImpersonate']);
    });
});
