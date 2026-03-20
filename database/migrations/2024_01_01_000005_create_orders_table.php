<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete(); // conservar historial aunque se elimine el usuario

            // Totales en Bolivianos (BS) — consistente con integración PayPal del proyecto
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);

            $table->enum('status', [
                'pending',    // esperando pago
                'paid',       // pago confirmado (PayPal)
                'processing', // en preparación
                'shipped',    // enviado
                'delivered',  // entregado
                'cancelled',  // cancelado
            ])->default('pending');

            // Datos de pago PayPal
            $table->string('paypal_order_id', 100)->nullable()->unique();
            $table->string('paypal_transaction_id', 100)->nullable();
            $table->timestamp('paid_at')->nullable();

            // Nota del cliente (campo opcional en checkout)
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
