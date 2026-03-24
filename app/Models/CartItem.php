<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Subtotal del ítem (precio * cantidad).
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->product->price * $this->quantity;
    }

    /**
     * Subtotal formateado en BS.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Bs. ' . number_format($this->subtotal, 2, '.', ',');
    }
}
