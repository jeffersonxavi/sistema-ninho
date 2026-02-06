<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se o usuário Admin já existe para evitar duplicatas
        if (User::where('email', 'admin@sistemaninho.com')->doesntExist()) {
            User::create([
                'name' => 'Administrador Principal',
                'email' => 'centroninho@gmail.com',
                'password' => Hash::make(env('ADMIN_PASSWORD')), // Altere 'password' para uma senha forte em produção!
                'role' => 'admin', // Define o papel como 'admin'
            ]);
            $this->command->info('Usuário Admin criado com sucesso!');
        } else {
            $this->command->info('Usuário Admin já existe.');
        }

        // Exemplo de usuário Staff (opcional)
        /* if (User::where('email', 'staff@sistemaninho.com')->doesntExist()) {
            User::create([
                'name' => 'Staff Comum',
                'email' => 'staff@sistemaninho.com',
                'password' => Hash::make('password'),
                'role' => 'staff', // Define o papel como 'staff'
            ]);
            $this->command->info('Usuário Staff criado com sucesso!');
        } */
    }
}