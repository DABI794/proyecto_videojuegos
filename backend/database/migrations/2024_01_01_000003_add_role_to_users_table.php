<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Laravel Breeze ya crea la tabla `users` con:
 *   id, name, email, email_verified_at, password, remember_token, timestamps
 *
 * Esta migration agrega los campos extra del proyecto:
 *   - role: distingue clientes de administradores
 *   - phone: teléfono opcional del cliente
 *
 * Ejecutar DESPUÉS de que Breeze haya creado su migration base.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['customer', 'admin'])->default('customer')->after('email');
            $table->string('phone', 20)->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone']);
        });
    }
};
