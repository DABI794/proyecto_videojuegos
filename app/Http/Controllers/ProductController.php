<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Catálogo con filtros — reemplaza productos.php del proyecto original.
     */
    public function index(Request $request): View
    {
        $query = Product::with('category')->active();

        // Filtro por categoría (query string: ?categoria=rpg)
        if ($request->filled('categoria')) {
            $query->byCategory($request->categoria);
        }

        // Filtro por búsqueda (query string: ?buscar=zelda)
        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        match ($request->get('orden', 'recientes')) {
            'precio_asc'  => $query->orderBy('price', 'asc'),
            'precio_desc' => $query->orderBy('price', 'desc'),
            'nombre'      => $query->orderBy('name', 'asc'),
            default       => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::active()->withCount(['products' => fn($q) => $q->active()])->get();

        // Categoría activa para resaltar en el filtro
        $activeCategory = $request->filled('categoria')
            ? Category::where('slug', $request->categoria)->first()
            : null;

        return view('products.index', compact('products', 'categories', 'activeCategory'));
    }

    /**
     * Detalle del producto — reemplaza info.php del proyecto original.
     */
    public function show(Product $product): View
    {
        // Si el producto está inactivo, 404
        abort_if(! $product->is_active, 404);

        // Productos relacionados de la misma categoría
        $relatedProducts = Product::with('category')
            ->active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
