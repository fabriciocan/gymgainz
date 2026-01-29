<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Lista todas as roles
     */
    public function index(): JsonResponse
    {
        $roles = Role::withCount('users')->get();

        return response()->json([
            'roles' => RoleResource::collection($roles)
        ]);
    }

    /**
     * Cria uma nova role
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'role' => new RoleResource($role),
            'message' => 'Permissão criada com sucesso!'
        ], 201);
    }

    /**
     * Exclui uma role
     */
    public function destroy(Role $role): JsonResponse
    {
        if ($role->name === 'admin') {
            return response()->json([
                'message' => 'Não é possível excluir a permissão de administrador.'
            ], 403);
        }

        $role->delete();

        return response()->json(['message' => 'Permissão excluída com sucesso!']);
    }
}
