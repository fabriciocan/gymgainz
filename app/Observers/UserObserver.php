<?php
namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function creating(User $user)
    {
        // Define todos os usuários como locais (isExterno = true para manter compatibilidade)
        $user->isExterno = true;
        Log::info('Usuário criado', ['email' => $user->email]);

        // Garante que todos os novos usuários estejam ativos por padrão
        if (! isset($user->active)) {
            $user->active = true;
        }
    }

    public function created(User $user)
    {
        Log::info('UserObserver@created called for user', ['email' => $user->email, 'id' => $user->id]);
    }
}
