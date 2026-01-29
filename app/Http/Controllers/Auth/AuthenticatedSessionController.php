<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('welcome');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $email = $request->email;

        // Verificar se o usuario existe e esta ativo
        $user = User::where('email', $email)
            ->orWhere('username', $email)
            ->first();

        if ($user && ! $user->active) {
            return back()->with('status', 'Sua conta esta desativada. Por favor, entre em contato com o administrador.');
        }

        try {
            $request->authenticate();
            $request->session()->regenerate();

            // Registrar o tipo de login bem-sucedido para fins de auditoria
            \Illuminate\Support\Facades\Log::info('Login bem-sucedido', [
                'user_id' => Auth::id(),
                'email'   => $email,
                'ip'      => $request->ip(),
                'user'    => Auth::user() ? [
                    'id'       => Auth::user()->id,
                    'email'    => Auth::user()->email,
                    'username' => Auth::user()->username,
                ] : null,
            ]);

            return redirect()->intended(RouteServiceProvider::HOME);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Erro na autenticacao', [
                'message' => $e->getMessage(),
                'email'   => $email,
            ]);

            return back()->withErrors([
                'email' => $e->getMessage(),
            ])->withInput(['email' => $request->email]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro inesperado na autenticacao', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()->with('status', 'Ocorreu um erro durante a autenticacao. Por favor, tente novamente ou entre em contato com o suporte.')->withInput(['email' => $request->email]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
