<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    // Categorías reales de videojuegos para datos realistas
    private array $gameCategories = [
        'Acción',
        'Aventura',
        'RPG',
        'Deportes',
        'Estrategia',
        'Terror',
        'Simulación',
        'Peleas',
        'Plataformas',
        'Shooter',
    ];

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement($this->gameCategories);

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $this->faker->sentence(8),
            'is_active'   => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
