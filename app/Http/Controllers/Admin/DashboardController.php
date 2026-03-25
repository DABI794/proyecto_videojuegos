<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Métricas básicas
        $stats = [
            'total_sales'   => Order::where('status', Order::STATUS_PAID)->sum('total'),
            'orders_count'  => Order::count(),
            'users_count'   => User::where('role', 'customer')->count(),
            'out_of_stock'  => Product::where('stock', 0)->count(),
        ];

        // Datos para el gráfico de ventas de los últimos 7 días
        $salesData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total')
            )
            ->where('status', Order::STATUS_PAID)
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Datos para el gráfico de categorías (distribución de productos)
        $categoryData = DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->select('categories.name', DB::raw('count(*) as total'))
            ->groupBy('categories.name')
            ->get();

        return view('admin.dashboard', compact('stats', 'salesData', 'categoryData'));
    }
}
