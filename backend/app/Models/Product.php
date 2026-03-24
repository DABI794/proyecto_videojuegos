<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasPriceFormatting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasPriceFormatting;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image_path',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'stock'       => 'integer',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Boot: auto-genera el slug
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('name') && ! $product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // -------------------------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------------------------

    /**
     * Un producto pertenece a una categoría.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Un producto puede estar en muchos ítems de carrito.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Un producto puede estar en muchos ítems de órdenes.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Devuelve la URL pública de la imagen o la imagen por defecto.
     * Compatible con el assets/default.jpg del proyecto original.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return asset('images/default.jpg');
    }

    /**
     * Precio formateado en Bolivianos (BS).
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->formatPrice($this->price);
    }


    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByCategory(Builder $query, int|string $category): Builder
    {
        return $query->whereHas('category', fn (Builder $q) =>
            is_int($category)
                ? $q->where('id', $category)
                : $q->where('slug', $category)
        );
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function decrementStock(int $quantity = 1): void
    {
        $this->decrement('stock', $quantity);
    }

    // -------------------------------------------------------------------------
    // Route model binding por slug
    // -------------------------------------------------------------------------

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
