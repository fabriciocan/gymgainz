<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login - Autenticação local
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'password.required' => 'O campo senha é obrigatório.',
        ]);

        $this->ensureIsNotRateLimited($request);

        try {
            $email = $request->email;

            Log::info('Tentativa de login', ['email' => $email]);

            // Busca o usuário
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::where('username', $email)->first();
            }

            // Verifica se está ativo antes de tentar autenticar
            if ($user && !$user->active) {
                return response()->json([
                    'message' => 'Sua conta está desativada. Por favor, entre em contato com o administrador.',
                    'error' => 'account_disabled'
                ], 403);
            }

            $credentials = [
                'email' => $user ? $user->email : $email,
                'password' => $request->password,
            ];

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                RateLimiter::clear($this->throttleKey($request));

                Log::info('Login bem-sucedido', [
                    'user_id' => Auth::id(),
                    'email' => $email,
                ]);

                return response()->json([
                    'user' => new UserResource(Auth::user()->load('roles')),
                    'message' => 'Login realizado com sucesso'
                ]);
            }

            RateLimiter::hit($this->throttleKey($request));

            return response()->json([
                'message' => 'Credenciais inválidas. Verifique seu email e senha.',
                'errors' => [
                    'email' => ['Credenciais inválidas. Verifique seu email e senha.']
                ]
            ], 422);
        } catch (ValidationException $e) {
            RateLimiter::hit($this->throttleKey($request));

            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    /**
     * Retorna usuário autenticado
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Não autenticado'], 401);
        }

        return response()->json([
            'user' => new UserResource($user->load('roles'))
        ]);
    }

    /**
     * Verifica rate limiting
     */
    private function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Gera a chave de rate limiting
     */
    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }
}
