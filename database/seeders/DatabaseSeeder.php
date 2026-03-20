<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Orden obligatorio:
     *  1. Users       (no depende de nada)
     *  2. Categories  (no depende de nada)
     *  3. Products    (depende de Categories)
     *
     * CartItems y Orders se crean manualmente durante las pruebas.
     */
    public function run(): void
    {
        $this->command->info('🎮 Iniciando seed de la tienda de videojuegos...');
        $this->command->newLine();

        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('🚀 Seed completado. Podés iniciar sesión en /login');
    }
}
