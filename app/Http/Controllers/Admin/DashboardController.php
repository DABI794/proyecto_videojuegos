<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Panel de administración — reemplaza admin/productos.php del proyecto original.
     */
    public function index(): View
    {
        $stats = [
            'total_products' => Product::active()->count(),
            'total_users'    => User::where('role', 'customer')->count(),
            'total_orders'   => Order::count(),
            'total_revenue'  => Order::where('status', '!=', Order::STATUS_CANCELLED)
                                     ->where('status', '!=', Order::STATUS_PENDING)
                                     ->sum('total'),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'low_stock'      => Product::active()->where('stock', '<=', 5)->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        $lowStockProducts = Product::with('category')
            ->active()
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockProducts'));
    }
}
