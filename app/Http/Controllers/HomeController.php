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
            ->withCount(['products' => fn($q) => $q->active()])
            ->having('products_count', '>', 0)
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
