<?php
namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        // Query builder para reuso
        $usersQuery = User::with('roles')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    // Usando LOWER para pesquisa case-insensitive
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%']);
                });
            })
            ->orderBy('name');

        // Paginacao para a tabela principal
        $users = $usersQuery->paginate(10);

        // Todos os usuarios para os modais (sem paginacao)
        $allUsers = $usersQuery->get();

        $roles = Role::all();

        return view('admin.users.roles', compact('users', 'allUsers', 'roles', 'search'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles'   => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->roles()->sync($request->roles);

        return redirect()->back()->with('success', 'Permissoes atualizadas com sucesso!');
    }

    public function massUpdate(Request $request)
    {
        $request->validate([
            'roles'      => 'required|array',
            'roles.*'    => 'exists:roles,id',
        ]);

        // Encontrar o role de admin
        $adminRole = Role::where('name', 'admin')->first();

        // Se vier a flag 'select_all', buscar todos os IDs conforme o filtro
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
                'user_ids'   => 'required|array',
                'user_ids.*' => 'exists:users,id',
            ]);
            $userIds = $request->user_ids;
        }

        User::whereIn('id', $userIds)->each(function ($user) use ($request, $adminRole) {
            // Se o usuario ja tem role admin, mantem
            if ($adminRole && $user->roles->contains($adminRole->id)) {
                $newRoles = collect($request->roles)
                    ->push($adminRole->id)
                    ->unique()
                    ->toArray();
                $user->roles()->sync($newRoles);
            } else {
                $newRoles = collect($request->roles)
                    ->reject(function ($roleId) use ($adminRole) {
                        return $adminRole && $roleId == $adminRole->id;
                    })
                    ->toArray();
                $user->roles()->sync($newRoles);
            }
        });

        return redirect()->back()->with('success', 'Permissoes atualizadas em massa com sucesso!');
    }

    public function toggleActive(User $user)
    {
        // Nao permite desativar usuario admin
        if ($user->hasRole('admin')) {
            return redirect()->back()->with('error', 'Nao e possivel desativar um usuario administrador.');
        }

        $user->active = ! $user->active;
        $user->save();

        $status = $user->active ? 'ativado' : 'desativado';
        return redirect()->back()->with('success', "Usuario {$status} com sucesso!");
    }

    /**
     * Exclui um usuario
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Nao permite excluir usuario admin
        if ($user->hasRole('admin')) {
            return redirect()->back()->with('error', 'Nao e possivel excluir um usuario administrador.');
        }

        try {
            $user->delete();
            return redirect()->back()->with('success', 'Usuario excluido com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir usuario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao excluir usuario. Por favor, tente novamente.');
        }
    }

    /**
     * Cria um novo usuario
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles'    => 'required|array',
            'roles.*'  => 'exists:roles,id',
        ]);

        try {
            // Gera o username a partir do email (parte antes do @)
            $username = explode('@', $request->email)[0];

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'username'  => $username,
                'password'  => bcrypt($request->password),
                'active'    => true,
                'isExterno' => true,
            ]);

            $user->roles()->sync($request->roles);

            return redirect()->back()->with('success', 'Usuario criado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao criar usuario. Por favor, tente novamente.');
        }
    }
}
