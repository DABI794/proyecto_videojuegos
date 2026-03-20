<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Categorías basadas en el esquema original del proyecto
     * (tabla `categorias`). Se usan datos reales de videojuegos.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Acción',
                'description' => 'Juegos de ritmo frenético con combate y reflejos.',
            ],
            [
                'name'        => 'Aventura',
                'description' => 'Explora mundos abiertos y resuelve misterios.',
            ],
            [
                'name'        => 'RPG',
                'description' => 'Juegos de rol con narrativa profunda y desarrollo de personaje.',
            ],
            [
                'name'        => 'Deportes',
                'description' => 'Fútbol, baloncesto, carreras y más deportes virtuales.',
            ],
            [
                'name'        => 'Estrategia',
                'description' => 'Planifica, construye y conquista con tu inteligencia.',
            ],
            [
                'name'        => 'Terror',
                'description' => 'Juegos de suspenso y horror que ponen a prueba tus nervios.',
            ],
            [
                'name'        => 'Simulación',
                'description' => 'Simula ciudades, granjas, aviones y mucho más.',
            ],
            [
                'name'        => 'Shooter',
                'description' => 'Acción en primera o tercera persona con armas de fuego.',
            ],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name'        => $data['name'],
                    'description' => $data['description'],
                    'is_active'   => true,
                ]
            );
        }

        $this->command->info('✅ Categorías creadas: ' . count($categories));
    }
}
