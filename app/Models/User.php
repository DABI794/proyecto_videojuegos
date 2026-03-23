<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // -------------------------------------------------------------------------
    // Campos asignables
    // -------------------------------------------------------------------------

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed', // Laravel 11: hash automático
        ];
    }

    // -------------------------------------------------------------------------
    // Helpers de rol
    // -------------------------------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    // -------------------------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------------------------

    /**
     * Los ítems del carrito del usuario.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Todas las órdenes del usuario.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Obtener las órdenes más recientes con sus ítems cargados.
     */
    public function latestOrders(int $limit = 10)
    {
        return $this->orders()
            ->with(['items.product.category'])
            ->latest()
            ->take($limit)
            ->get();
    }


    // -------------------------------------------------------------------------
    // Helpers de carrito
    // -------------------------------------------------------------------------

    /**
     * Cantidad total de ítems (suma de quantities) para el badge del navbar.
     */
    public function cartCount(): int
    {
        return (int) $this->cartItems()->sum('quantity');
    }

    /**
     * Total en BS del carrito actual.
     */
    public function cartTotal(): float
    {
        return (float) $this->cartItems()
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->sum(\DB::raw('cart_items.quantity * products.price'));
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}

