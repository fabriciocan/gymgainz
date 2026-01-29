<?php
namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'O campo email é obrigatório.',
            'email.email'       => 'Digite um email válido.',
            'password.required' => 'O campo senha é obrigatório.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        try {
            $email = $this->email;

            Log::info('Tentativa de login', ['email' => $email]);

            // Busca o usuário
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::where('username', $email)->first();
            }

            // Verifica se está ativo antes de tentar autenticar
            if ($user && !$user->active) {
                throw ValidationException::withMessages([
                    'email' => 'Sua conta está desativada. Por favor, entre em contato com o administrador.',
                ]);
            }

            $credentials = [
                'email'    => $user ? $user->email : $email,
                'password' => $this->password,
            ];

            if (Auth::attempt($credentials, $this->boolean('remember'))) {
                RateLimiter::clear($this->throttleKey());
                return;
            }

            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => 'Credenciais inválidas. Verifique seu email e senha.',
            ]);
        } catch (ValidationException $e) {
            Log::error('Erro na autenticação', [
                'messages' => $e->getMessage(),
                'email'    => $email ?? null,
            ]);
            throw $e;
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')) . '|' . $this->ip());
    }
}
