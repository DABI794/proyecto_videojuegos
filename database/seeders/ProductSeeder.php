<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Mapeo de productos por categoría (nombre de categoría => productos)
        $catalog = [
            'Acción' => [
                ['name' => 'Devil May Cry 5',        'price' => 189.00, 'stock' => 15, 'featured' => true],
                ['name' => 'Bayonetta 3',             'price' => 210.00, 'stock' => 8,  'featured' => false],
                ['name' => 'Metal Gear Rising',       'price' => 120.00, 'stock' => 20, 'featured' => false],
                ['name' => 'Sekiro: Shadows Die Twice','price' => 230.00, 'stock' => 12, 'featured' => true],
            ],
            'Aventura' => [
                ['name' => 'The Legend of Zelda: BotW', 'price' => 270.00, 'stock' => 10, 'featured' => true],
                ['name' => 'Uncharted 4',               'price' => 150.00, 'stock' => 18, 'featured' => false],
                ['name' => 'Horizon Forbidden West',    'price' => 240.00, 'stock' => 7,  'featured' => true],
                ['name' => 'Kena: Bridge of Spirits',   'price' => 130.00, 'stock' => 14, 'featured' => false],
            ],
            'RPG' => [
                ['name' => 'Elden Ring',               'price' => 250.00, 'stock' => 20, 'featured' => true],
                ['name' => 'Final Fantasy XVI',         'price' => 280.00, 'stock' => 9,  'featured' => true],
                ['name' => 'Baldur\'s Gate 3',          'price' => 260.00, 'stock' => 11, 'featured' => false],
                ['name' => 'Dragon\'s Dogma 2',         'price' => 255.00, 'stock' => 6,  'featured' => false],
            ],
            'Deportes' => [
                ['name' => 'EA Sports FC 25',          'price' => 220.00, 'stock' => 30, 'featured' => true],
                ['name' => 'NBA 2K25',                  'price' => 200.00, 'stock' => 22, 'featured' => false],
                ['name' => 'Gran Turismo 7',            'price' => 210.00, 'stock' => 15, 'featured' => false],
                ['name' => 'WWE 2K24',                  'price' => 190.00, 'stock' => 12, 'featured' => false],
            ],
            'Estrategia' => [
                ['name' => 'Civilization VII',         'price' => 240.00, 'stock' => 8,  'featured' => false],
                ['name' => 'StarCraft II',              'price' => 90.00,  'stock' => 25, 'featured' => false],
                ['name' => 'Age of Empires IV',         'price' => 170.00, 'stock' => 13, 'featured' => false],
                ['name' => 'XCOM 3',                    'price' => 160.00, 'stock' => 10, 'featured' => false],
                ['name' => 'Age of empires II',            'price' => 66.00, 'stock' => 4, 'featured' => false],
            ],
            'Terror' => [
                ['name' => 'Resident Evil 4 Remake',  'price' => 230.00, 'stock' => 17, 'featured' => true],
                ['name' => 'Silent Hill 2 Remake',     'price' => 245.00, 'stock' => 9,  'featured' => true],
                ['name' => 'Alien: Isolation',          'price' => 100.00, 'stock' => 20, 'featured' => false],
                ['name' => 'Outlast Trials',            'price' => 120.00, 'stock' => 14, 'featured' => false],
                
            ],
            'Simulación' => [
                ['name' => 'Microsoft Flight Simulator', 'price' => 270.00, 'stock' => 6, 'featured' => false],
                ['name' => 'Cities: Skylines 2',          'price' => 200.00, 'stock' => 9, 'featured' => false],
                ['name' => 'Farming Simulator 25',        'price' => 180.00, 'stock' => 11,'featured' => false],
                ['name' => 'Planet Zoo',                   'price' => 140.00, 'stock' => 15,'featured' => false],
            ],
            'Shooter' => [
                ['name' => 'Cyberpunk 2077',           'price' => 200.00, 'stock' => 25, 'featured' => true],
                ['name' => 'Doom Eternal',              'price' => 130.00, 'stock' => 18, 'featured' => false],
                ['name' => 'Borderlands 3',             'price' => 110.00, 'stock' => 22, 'featured' => false],
                ['name' => 'Halo Infinite',             'price' => 170.00, 'stock' => 14, 'featured' => false],
            ],
        ];

        $total = 0;

        foreach ($catalog as $categoryName => $products) {
            $category = Category::where('slug', Str::slug($categoryName))->first();

            if (! $category) {
                $this->command->warn("⚠️  Categoría '{$categoryName}' no encontrada. Ejecuta CategorySeeder primero.");
                continue;
            }

            foreach ($products as $data) {
                Product::firstOrCreate(
                    ['slug' => Str::slug($data['name'])],
                    [
                        'category_id' => $category->id,
                        'name'        => $data['name'],
                        'description' => "Disfruta de {$data['name']}, uno de los mejores títulos en la categoría {$categoryName}. Disponible en nuestra tienda con envío a todo Bolivia.",
                        'price'       => $data['price'],
                        'stock'       => $data['stock'],
                        'image_path'  => null, // fallback a default.jpg
                        'is_active'   => true,
                        'is_featured' => $data['featured'],
                    ]
                );
                $total++;
            }
        }

        $this->command->info("✅ Productos creados: {$total}");
    }
}
