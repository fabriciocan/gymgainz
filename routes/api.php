<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImpersonateController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Gym\BodyMeasurementController;
use App\Http\Controllers\Api\Gym\DashboardController;
use App\Http\Controllers\Api\Gym\ExerciseController;
use App\Http\Controllers\Api\Gym\ProgressController;
use App\Http\Controllers\Api\Gym\SessionSetController;
use App\Http\Controllers\Api\Gym\SubscriptionController;
use App\Http\Controllers\Api\Gym\TrainingSessionController;
use App\Http\Controllers\Api\Gym\WebhookController;
use App\Http\Controllers\Api\Gym\WorkoutController;
use App\Http\Controllers\Api\Gym\WorkoutExerciseController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Webhook sem autenticação
Route::post('/webhook/abacatepay', [WebhookController::class, 'handle']);

// Rotas protegidas (requerem autenticação)
Route::middleware('auth:sanctum')->group(function () {
    // Autenticação
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Roles
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/impersonate/status', [ImpersonateController::class, 'status']);

    // Assinatura (sem subscription middleware)
    Route::get('/subscription/status', [SubscriptionController::class, 'status']);
    Route::post('/subscription/create', [SubscriptionController::class, 'create']);

    // Rotas que exigem assinatura ativa
    Route::middleware('subscription')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Exercícios
        Route::get('/exercises', [ExerciseController::class, 'index']);
        Route::post('/exercises', [ExerciseController::class, 'store']);
        Route::put('/exercises/{exercise}', [ExerciseController::class, 'update']);
        Route::delete('/exercises/{exercise}', [ExerciseController::class, 'destroy']);

        // Treinos
        Route::get('/workouts', [WorkoutController::class, 'index']);
        Route::post('/workouts', [WorkoutController::class, 'store']);
        Route::get('/workouts/{workout}', [WorkoutController::class, 'show']);
        Route::put('/workouts/{workout}', [WorkoutController::class, 'update']);
        Route::delete('/workouts/{workout}', [WorkoutController::class, 'destroy']);

        // Exercícios do treino
        Route::get('/workouts/{workout}/exercises', [WorkoutExerciseController::class, 'index']);
        Route::post('/workouts/{workout}/exercises', [WorkoutExerciseController::class, 'store']);
        Route::put('/workouts/{workout}/exercises/{exercise}', [WorkoutExerciseController::class, 'update']);
        Route::delete('/workouts/{workout}/exercises/{exercise}', [WorkoutExerciseController::class, 'destroy']);
        Route::post('/workouts/{workout}/exercises/reorder', [WorkoutExerciseController::class, 'reorder']);

        // Sessões de treino
        Route::get('/sessions', [TrainingSessionController::class, 'index']);
        Route::post('/sessions', [TrainingSessionController::class, 'store']);
        Route::get('/sessions/{session}', [TrainingSessionController::class, 'show']);
        Route::put('/sessions/{session}/finish', [TrainingSessionController::class, 'finish']);
        Route::delete('/sessions/{session}', [TrainingSessionController::class, 'destroy']);
        Route::get('/sessions/{session}/previous', [TrainingSessionController::class, 'previous']);

        // Sets da sessão
        Route::post('/sessions/{session}/sets', [SessionSetController::class, 'store']);
        Route::put('/sessions/{session}/sets/{set}', [SessionSetController::class, 'update']);
        Route::delete('/sessions/{session}/sets/{set}', [SessionSetController::class, 'destroy']);

        // Progressão
        Route::get('/progress/exercise/{exercise}', [ProgressController::class, 'exercise']);
        Route::get('/progress/exercise/{exercise}/volume', [ProgressController::class, 'volume']);

        // Medidas corporais
        Route::get('/measurements/latest', [BodyMeasurementController::class, 'latest']);
        Route::get('/measurements', [BodyMeasurementController::class, 'index']);
        Route::post('/measurements', [BodyMeasurementController::class, 'store']);
        Route::put('/measurements/{measurement}', [BodyMeasurementController::class, 'update']);
        Route::delete('/measurements/{measurement}', [BodyMeasurementController::class, 'destroy']);
    });

    // Rotas administrativas
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}/roles', [UserController::class, 'updateRoles']);
        Route::put('/users/{user}/toggle-active', [UserController::class, 'toggleActive']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::post('/users/mass-update-roles', [UserController::class, 'massUpdateRoles']);
        Route::get('/check-email', [UserController::class, 'checkEmail']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
        Route::post('/impersonate/{user}', [ImpersonateController::class, 'impersonate']);
        Route::post('/stop-impersonate', [ImpersonateController::class, 'stopImpersonate']);
    });
});
