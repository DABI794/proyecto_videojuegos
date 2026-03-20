<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ────────────────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@videojuegos.bo'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('Admin@12345!'),
                'role'     => 'admin',
                'phone'    => '+591 70000000',
            ]
        );

        // ── Clientes de prueba ────────────────────────────────────────────────
        $customers = [
            [
                'name'     => 'Juan Pérez',
                'email'    => 'juan@example.com',
                'password' => Hash::make('password'),
                'phone'    => '+591 71234567',
            ],
            [
                'name'     => 'María García',
                'email'    => 'maria@example.com',
                'password' => Hash::make('password'),
                'phone'    => '+591 76543210',
            ],
            [
                'name'     => 'Carlos López',
                'email'    => 'carlos@example.com',
                'password' => Hash::make('password'),
                'phone'    => null,
            ],
        ];

        foreach ($customers as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['role' => 'customer'])
            );
        }

        $this->command->info('✅ Usuarios creados:');
        $this->command->line('   Admin    → admin@videojuegos.bo / Admin@12345!');
        $this->command->line('   Cliente  → juan@example.com / password');
        $this->command->line('   Cliente  → maria@example.com / password');
        $this->command->line('   Cliente  → carlos@example.com / password');
    }
}
