<?php

declare(strict_types=1);

namespace App\Traits;

trait HasPriceFormatting
{
    /**
     * Formatear un valor numérico como moneda (Bs. X.XX).
     */
    public function formatPrice(float|string|null $value): string
    {
        return 'Bs. ' . number_format((float) ($value ?? 0), 2, '.', ',');
    }

    /**
     * Accessor genérico para obtener el precio formateado.
     * Se puede usar como $model->formatted_price si el modelo tiene 'price'.
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->formatPrice($this->price ?? 0);
    }
}
