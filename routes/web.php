<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| As rotas web agora são mínimas pois o frontend é servido pelo Nuxt.js.
| Apenas mantemos as rotas necessárias para o Sanctum funcionar.
|
*/

// Rota para o CSRF cookie do Sanctum (necessária para SPA)
// A rota /sanctum/csrf-cookie é registrada automaticamente pelo Sanctum

// Rota de debug apenas em desenvolvimento
if (app()->environment('local')) {
    Route::get('/debug/auth', function () {
        $output = [
            'auth_guards'    => config('auth.guards'),
            'auth_providers' => config('auth.providers'),
        ];

        return response()->json($output);
    });
}
