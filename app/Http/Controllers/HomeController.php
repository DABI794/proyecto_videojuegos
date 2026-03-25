<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::with('category')
            ->active()
            ->featured()
            ->inStock()
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::active()
            ->whereHas('products', fn($q) => $q->active()) // Esto filtra que tengan productos activos
            ->withCount(['products' => fn($q) => $q->active()]) // Esto trae el número
            ->get();

        $latestProducts = Product::with('category')
            ->active()
            ->inStock()
            ->latest()
            ->take(4)
            ->get();

        return view('home', compact('featuredProducts', 'categories', 'latestProducts'));
    }
}