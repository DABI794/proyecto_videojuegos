<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    // Videojuegos reales/realistas para demo
    private array $gameTitles = [
        'The Last Horizon',
        'Dragon Forge Chronicles',
        'Neon Runners',
        'Shadow Protocol',
        'Starfall Arena',
        'Crystal Dungeon',
        'Mech Warriors Elite',
        'Phantom Coast',
        'Turbo Champions',
        'Dark Covenant',
        'Sky Pirates',
        'Iron Fortress',
        'Void Hunters',
        'Eternal Realm',
        'Speed Rush GT',
        'Crimson Blade',
        'Ocean Odyssey',
        'Pixel Warriors',
        'Thunder Strike',
        'Lost Kingdom',
    ];

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement($this->gameTitles);

        return [
            'category_id' => Category::factory(),
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $this->faker->paragraphs(2, true),
            // Precios en rango realista para videojuegos en Bolivia (BS)
            'price'       => $this->faker->randomFloat(2, 50, 450),
            'stock'       => $this->faker->numberBetween(0, 100),
            'image_path'  => null, // se usará default.jpg
            'is_active'   => true,
            'is_featured' => false,
        ];
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }

    public function outOfStock(): static
    {
        return $this->state(['stock' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function forCategory(Category $category): static
    {
        return $this->state(['category_id' => $category->id]);
    }
}
