<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Lista usuários com paginação
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->search;
        $perPage = $request->input('per_page', 10);

        $users = User::with('roles')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%']);
                });
            })
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'users' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    /**
     * Exibe um usuário específico
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($user->load('roles'))
        ]);
    }

    /**
     * Cria usuário
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        try {
            $username = explode('@', $request->email)[0];

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $username,
                'password' => bcrypt($request->password),
                'active' => true,
                'isExterno' => true,
            ]);

            $user->roles()->sync($request->roles);

            return response()->json([
                'user' => new UserResource($user->load('roles')),
                'message' => 'Usuário criado com sucesso!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao criar usuário.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza roles do usuário
     */
    public function updateRoles(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->roles()->sync($request->roles);

        return response()->json([
            'user' => new UserResource($user->load('roles')),
            'message' => 'Permissões atualizadas com sucesso!'
        ]);
    }

    /**
     * Edição em massa de roles
     */
    public function massUpdateRoles(Request $request): JsonResponse
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $adminRole = Role::where('name', 'admin')->first();

        // Se select_all, busca todos os usuários conforme filtro
        if ($request->has('select_all') && $request->select_all) {
            $usersQuery = User::query();
            if ($request->filled('search')) {
                $search = $request->search;
                $usersQuery->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%']);
                });
            }
            $userIds = $usersQuery->pluck('id')->toArray();
        } else {
            $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
            ]);
            $userIds = $request->user_ids;
        }

        $updatedCount = 0;
        User::whereIn('id', $userIds)->each(function ($user) use ($request, $adminRole, &$updatedCount) {
            if ($adminRole && $user->roles->contains($adminRole->id)) {
                $newRoles = collect($request->roles)
                    ->push($adminRole->id)
                    ->unique()
                    ->toArray();
            } else {
                $newRoles = collect($request->roles)
                    ->reject(fn($roleId) => $adminRole && $roleId == $adminRole->id)
                    ->toArray();
            }
            $user->roles()->sync($newRoles);
            $updatedCount++;
        });

        return response()->json([
            'message' => "Permissões atualizadas em {$updatedCount} usuários!",
            'updated_count' => $updatedCount
        ]);
    }

    /**
     * Toggle status ativo/inativo
     */
    public function toggleActive(User $user): JsonResponse
    {
        if ($user->hasRole('admin')) {
            return response()->json([
                'message' => 'Não é possível desativar um usuário administrador.'
            ], 403);
        }

        $user->active = !$user->active;
        $user->save();

        return response()->json([
            'user' => new UserResource($user->load('roles')),
            'message' => 'Usuário ' . ($user->active ? 'ativado' : 'desativado') . ' com sucesso!'
        ]);
    }

    /**
     * Exclui usuário
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user->hasRole('admin')) {
            return response()->json([
                'message' => 'Não é possível excluir um usuário administrador.'
            ], 403);
        }

        try {
            $user->delete();
            return response()->json(['message' => 'Usuário excluído com sucesso!']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir usuário.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica se email existe
     */
    public function checkEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }
}
