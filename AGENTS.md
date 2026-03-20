# AGENTS.md — Developer Guide for AI Coding Agents

> **Target Audience:** AI coding assistants operating in the `tienda-videojuegos` repository.  
> **Project:** Laravel 12 e-commerce application for video game sales in Bolivia.  
> **Language:** Backend (PHP 8.2), Frontend (Blade + Alpine.js), Spanish comments in business logic.

---

## 1. Project Overview

**Stack:** Laravel 12 + MySQL 8.0 + Tailwind CSS + Vite + PayPal SDK  
**Authentication:** Laravel Breeze (with role-based authorization)  
**Key Models:** User, Product, Category, CartItem, Order, OrderItem

This is a **refactored project** migrated from legacy PHP to Laravel 12. Comments often reference the original system (e.g., "reemplaza productos.php del proyecto original").

---

## 2. Build, Test, and Development Commands

### Development Server
```bash
# Start all services concurrently (server, queue, logs, vite)
composer dev

# Or individually:
php artisan serve                    # Backend server (http://localhost:8000)
npm run dev                          # Vite dev server (hot reload)
php artisan queue:listen --tries=1   # Queue worker
php artisan pail --timeout=0         # Real-time logs
```

### Build Commands
```bash
npm run build                        # Production asset build (Vite)
php artisan optimize                 # Cache routes, config, views
php artisan optimize:clear           # Clear all caches
```

### Linting and Formatting
```bash
./vendor/bin/pint                    # Format PHP code (Laravel Pint)
./vendor/bin/pint --test             # Check formatting without changes
./vendor/bin/pint app/Models         # Format specific directory
```

### Testing
```bash
php artisan test                     # Run all tests (PHPUnit)
php artisan test --filter=AuthenticationTest  # Single test class
php artisan test tests/Feature/Auth/AuthenticationTest.php  # Single file
php artisan test --testsuite=Feature # Run only Feature tests
php artisan test --testsuite=Unit    # Run only Unit tests
php artisan test --parallel          # Parallel execution
```

### Database
```bash
php artisan migrate                  # Run migrations
php artisan migrate:fresh --seed     # Reset DB and seed data
php artisan db:seed                  # Seed data only
php artisan migrate:rollback         # Rollback last migration
php artisan migrate:status           # Check migration status
```

### Utilities
```bash
php artisan route:list               # List all routes
php artisan make:controller ProductController  # Generate controller
php artisan make:model Product -mfsc # Model + migration, factory, seeder, controller
php artisan make:request StoreProductRequest   # Form request validation
php artisan tinker                   # Interactive console
```

---

## 3. Code Style Guidelines

### General Principles
- **Strict Types:** Every PHP file MUST start with `declare(strict_types=1);`
- **Type Safety:** Always use explicit type hints for parameters and return types
- **Spanish Comments:** Business logic comments in Spanish, technical comments in English when needed
- **EditorConfig:** 4 spaces for PHP, 2 spaces for YAML/JSON, LF line endings

### File Organization

#### Controllers
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;  // Custom requests
use App\Models\Category;                     // Models
use App\Models\Product;
use Illuminate\Http\JsonResponse;           // Laravel classes
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    // Method order: index → create → store → show → edit → update → destroy
    
    public function index(Request $request): View
    {
        // Implementation
    }
}
```

#### Models
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    // -------------------------------------------------------------------------
    // Properties
    // -------------------------------------------------------------------------
    
    protected $fillable = ['name', 'slug', 'description', 'price', 'stock'];
    
    protected $casts = [
        'price'       => 'decimal:2',
        'stock'       => 'integer',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Boot: auto-generation and event listeners
    // -------------------------------------------------------------------------
    
    protected static function booted(): void
    {
        // Auto-generate slugs, etc.
    }

    // -------------------------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------------------------
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------
    
    public function getFormattedPriceAttribute(): string
    {
        return 'Bs. ' . number_format((float) $this->price, 2, '.', ',');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------
    
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------
    
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
}
```

### Imports
- **Order:** Controllers first → Requests → Models → Laravel classes → Third-party
- **Alphabetical:** Within each group
- **Avoid:** `use Illuminate\Support\Facades\*` unless needed

### Naming Conventions

| Element | Convention | Example |
|---------|-----------|---------|
| **Controllers** | PascalCase + `Controller` suffix | `ProductController`, `Admin\DashboardController` |
| **Models** | Singular PascalCase | `Product`, `OrderItem`, `CartItem` |
| **Methods** | camelCase | `index()`, `getFormattedPrice()`, `isAdmin()` |
| **Routes** | Dot notation, plural resources | `products.index`, `admin.productos.store` |
| **Views** | Kebab-case | `product-card.blade.php`, `checkout-form.blade.php` |
| **Database Columns** | snake_case | `created_at`, `is_active`, `user_id` |
| **Form Requests** | `{Action}{Model}Request` | `StoreProductRequest`, `UpdateProductRequest` |
| **Test Methods** | `test_snake_case_description` | `test_users_can_authenticate()` |

### Query Best Practices
```php
// ✅ ALWAYS eager load relationships to prevent N+1 queries
$products = Product::with('category')->active()->paginate(12);

// ✅ Use query scopes for reusable filters
$query = Product::active()->featured()->inStock();

// ✅ Use route model binding instead of manual queries
Route::get('/productos/{product}', [ProductController::class, 'show']);

// ❌ NEVER construct raw SQL with concatenation
// Use Eloquent or query builder with bindings
```

### Error Handling and Validation
```php
// ✅ Use Form Requests for validation
public function store(StoreProductRequest $request): RedirectResponse
{
    // Validation already passed
}

// ✅ Use abort_if() for inline authorization
abort_if($cartItem->user_id !== auth()->id(), 403);

// ✅ Handle AJAX and form submissions in one method
public function store(Request $request): JsonResponse|RedirectResponse
{
    if ($request->expectsJson()) {
        return response()->json(['success' => true]);
    }
    return back()->with('success', 'Operación exitosa');
}

// ✅ Use try-catch for external APIs (PayPal, etc.)
try {
    $result = $paypalService->capturePayment($orderId);
} catch (\Exception $e) {
    return back()->withErrors(['payment' => $e->getMessage()]);
}
```

### Blade Templates
```blade
{{-- File: resources/views/products/show.blade.php --}}

@extends('layouts.app')

@section('title', $product->name)

@section('content')
    {{-- Always include CSRF in forms --}}
    <form action="{{ route('cart.store') }}" method="POST">
        @csrf
        
        {{-- Display validation errors --}}
        @error('product_id')
            <p class="text-red-500 text-sm">{{ $message }}</p>
        @enderror
        
        {{-- Use named routes, not hardcoded URLs --}}
        <a href="{{ route('products.index') }}">Volver al catálogo</a>
    </form>
@endsection

@push('scripts')
    <script>
        // AJAX with CSRF token from meta tag
        fetch('{{ route("cart.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ product_id: {{ $product->id }} })
        });
    </script>
@endpush
```

---

## 4. Database Conventions

- **Table Names:** Plural snake_case (`products`, `order_items`, `cart_items`)
- **Foreign Keys:** `{model}_id` (e.g., `user_id`, `category_id`)
- **Timestamps:** Always include `timestamps()` in migrations
- **Soft Deletes:** Use for models with historical references (e.g., `products`)
- **Constraints:** Define foreign keys with `constrained()->cascadeOnDelete()` when appropriate
- **Enums/States:** Use constants in models for magic strings:
  ```php
  const STATUS_PENDING = 'pending';
  const STATUS_PAID = 'paid';
  ```

---

## 5. Security Checklist

- ✅ **CSRF Protection:** Always include `@csrf` in forms
- ✅ **Mass Assignment:** Define `$fillable` or `$guarded` in all models
- ✅ **Authorization:** Use middleware (`auth`, `admin`) and `abort_if()` checks
- ✅ **Password Hashing:** Use `hashed` cast in User model (automatic bcrypt)
- ✅ **SQL Injection:** Use Eloquent or query builder with bindings (NEVER raw concatenation)
- ✅ **Environment Variables:** Store secrets in `.env` (excluded from Git)
- ✅ **Rate Limiting:** Apply to login/API routes via middleware

---

## 6. Common Patterns in This Codebase

### Dual Response Pattern (AJAX + Form)
```php
public function store(Request $request): JsonResponse|RedirectResponse
{
    // ... business logic ...
    
    if ($request->expectsJson()) {
        return response()->json(['exito' => true, 'cantidad' => $cartCount]);
    }
    return back()->with('success', 'Producto agregado al carrito');
}
```

### Route Model Binding with Soft Deletes
```php
// In routes/web.php
Route::get('/productos/{product}', [ProductController::class, 'show']);

// In controller: $product is automatically resolved
public function show(Product $product): View
{
    // Includes soft-deleted products if relationship uses withTrashed()
}
```

### Match Expression for Clean Conditionals
```php
match ($request->get('orden', 'recientes')) {
    'precio_asc'  => $query->orderBy('price', 'asc'),
    'precio_desc' => $query->orderBy('price', 'desc'),
    'nombre'      => $query->orderBy('name', 'asc'),
    default       => $query->latest(),
};
```

### Snapshot Pattern for Historical Integrity
```php
// Store product name/price at purchase time to avoid breakage if product changes
OrderItem::create([
    'order_id'     => $order->id,
    'product_id'   => $product->id,
    'product_name' => $product->name,    // Snapshot
    'unit_price'   => $product->price,   // Snapshot
    'quantity'     => $item->quantity,
]);
```

---

## 7. Project-Specific Notes

- **Currency:** All prices displayed as "Bs. X,XXX.XX" (Bolivianos)
- **Timezone:** America/La_Paz (see `config/app.php`)
- **Locale:** Spanish (`es`) for UI and validation messages
- **PayPal:** Sandbox mode by default (`PAYPAL_MODE=sandbox` in `.env`)
- **Admin Role:** Checked via `auth()->user()->isAdmin()` or middleware `admin`
- **Soft Deletes:** Applied to `products` to preserve order history integrity
- **Image Storage:** Products store paths in `image_path` column, files in `storage/app/public/products/`

---

## 8. When Adding New Features

1. **Run migrations first:** `php artisan migrate` after creating new migrations
2. **Use Form Requests:** Create dedicated validation classes for store/update operations
3. **Add Query Scopes:** For reusable filters (e.g., `scopePublished()`, `scopeByStatus()`)
4. **Eager Load Relations:** Always use `with()` when accessing related models in loops
5. **Update Routes:** Use named routes and resource routing when possible
6. **Test Authorization:** Ensure middleware and authorization checks are in place
7. **Run Pint:** Format code before committing (`./vendor/bin/pint`)
8. **Update Tests:** Add feature tests for new user-facing functionality

---

## 9. References

- **Documentation:** [Laravel 12 Docs](https://laravel.com/docs/12.x)
- **Breeze Auth:** [Laravel Breeze](https://laravel.com/docs/12.x/starter-kits#laravel-breeze)
- **Tailwind CSS:** [Tailwind v3 Docs](https://tailwindcss.com/docs)
- **Color Palette:** Custom dark theme (see `resources/css/app.css` and `tailwind.config.js`)
  - Primary: `#6366f1` (indigo)
  - Surface: `#0f172a`, `#1e293b`, `#334155`
- **PayPal SDK:** [PayPal Developer](https://developer.paypal.com/)

---

**Last Updated:** March 2026  
**Maintained By:** AI Coding Agents + Human Developers
