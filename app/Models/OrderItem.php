<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity'   => 'integer',
        'subtotal'   => 'decimal:2',
    ];

    // -------------------------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------------------------

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relación nullable: el producto puede estar eliminado (softDelete)
     * pero el ítem conserva su snapshot (product_name, unit_price).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getFormattedUnitPriceAttribute(): string
    {
        return 'Bs. ' . number_format((float) $this->unit_price, 2, '.', ',');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Bs. ' . number_format((float) $this->subtotal, 2, '.', ',');
    }
}
