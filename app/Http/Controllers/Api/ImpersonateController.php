<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonateController extends Controller
{
    /**
     * Impersonar um usuário
     */
    public function impersonate(User $user): JsonResponse
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json([
                'message' => 'Apenas administradores podem usar esta funcionalidade.'
            ], 403);
        }

        if ($user->hasRole('admin')) {
            return response()->json([
                'message' => 'Não é possível impersonar outro administrador.'
            ], 403);
        }

        Session::put('original_user_id', Auth::id());
        Session::put('impersonate', $user->id);

        Auth::login($user);

        return response()->json([
            'user' => new UserResource($user->load('roles')),
            'message' => 'Você está agora visualizando como ' . $user->name,
            'impersonating' => true
        ]);
    }

    /**
     * Parar de impersonar
     */
    public function stopImpersonate(): JsonResponse
    {
        if (!Session::has('original_user_id')) {
            return response()->json([
                'message' => 'Você não está impersonando nenhum usuário.'
            ], 400);
        }

        $originalUser = User::find(Session::get('original_user_id'));

        Session::forget('original_user_id');
        Session::forget('impersonate');

        Auth::login($originalUser);

        return response()->json([
            'user' => new UserResource($originalUser->load('roles')),
            'message' => 'Você voltou para sua conta original.',
            'impersonating' => false
        ]);
    }

    /**
     * Verifica se está impersonando
     */
    public function status(): JsonResponse
    {
        $isImpersonating = Session::has('original_user_id');
        $originalUserId = Session::get('original_user_id');

        return response()->json([
            'impersonating' => $isImpersonating,
            'original_user_id' => $originalUserId
        ]);
    }
}
