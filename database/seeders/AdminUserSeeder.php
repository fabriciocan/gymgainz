<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Busca ou cria o role admin
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrador do Sistema']
        );

        // Cria o usuario admin se nao existir
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'active' => true,
                'isExterno' => true,
            ]
        );

        // Atribui o role admin ao usuario
        if (!$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole->id);
        }

        $this->command->info('Usuario admin criado com sucesso!');
        $this->command->info('Email: admin@admin.com');
        $this->command->info('Senha: admin123');
        $this->command->warn('IMPORTANTE: Altere a senha apos o primeiro login!');
    }
}
