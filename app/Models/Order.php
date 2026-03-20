<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subtotal',
        'total',
        'status',
        'paypal_order_id',
        'paypal_transaction_id',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total'    => 'decimal:2',
        'paid_at'  => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Estados disponibles (constantes para evitar strings mágicos)
    // -------------------------------------------------------------------------

    const STATUS_PENDING    = 'pending';
    const STATUS_PAID       = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';

    // -------------------------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getFormattedTotalAttribute(): string
    {
        return 'Bs. ' . number_format((float) $this->total, 2, '.', ',');
    }

    /**
     * Etiqueta legible del estado (en español, para las vistas).
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING    => 'Pendiente',
            self::STATUS_PAID       => 'Pagado',
            self::STATUS_PROCESSING => 'En proceso',
            self::STATUS_SHIPPED    => 'Enviado',
            self::STATUS_DELIVERED  => 'Entregado',
            self::STATUS_CANCELLED  => 'Cancelado',
            default                 => ucfirst($this->status),
        };
    }

    /**
     * Color Tailwind para el badge de estado (usa la paleta del diseño).
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING    => 'text-yellow-400 bg-yellow-400/10',
            self::STATUS_PAID       => 'text-emerald-400 bg-emerald-400/10',
            self::STATUS_PROCESSING => 'text-indigo-400 bg-indigo-400/10',
            self::STATUS_SHIPPED    => 'text-blue-400 bg-blue-400/10',
            self::STATUS_DELIVERED  => 'text-green-400 bg-green-400/10',
            self::STATUS_CANCELLED  => 'text-red-400 bg-red-400/10',
            default                 => 'text-slate-400 bg-slate-400/10',
        };
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isPaid(): bool
    {
        return $this->status !== self::STATUS_PENDING
            && $this->status !== self::STATUS_CANCELLED;
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PAID]);
    }
}
