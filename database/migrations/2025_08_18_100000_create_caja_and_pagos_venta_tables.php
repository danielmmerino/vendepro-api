<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('caja_aperturas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('local_id');
            $table->unsignedBigInteger('caja_id');
            $table->unsignedBigInteger('usuario_id');
            $table->decimal('saldo_inicial', 12, 2)->default(0);
            $table->timestamp('abierto_at')->useCurrent();
            $table->timestamp('cerrado_at')->nullable();
            $table->string('estado', 20)->default('abierta');
            $table->string('observacion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('caja_movimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apertura_id');
            $table->string('tipo', 20);
            $table->decimal('monto', 12, 2);
            $table->string('motivo');
            $table->string('referencia')->nullable();
            $table->string('banco')->nullable();
            $table->date('fecha_deposito')->nullable();
            $table->timestamps();
        });

        Schema::create('caja_cierres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apertura_id');
            $table->decimal('esperado_efectivo', 12, 2);
            $table->decimal('contado_efectivo', 12, 2);
            $table->decimal('diferencia', 12, 2);
            $table->json('detalle_conteo')->nullable();
            $table->string('observacion')->nullable();
            $table->timestamps();
        });

        Schema::create('catalogo_metodos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('pagos_venta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('factura_id');
            $table->decimal('total', 12, 2);
            $table->decimal('propina', 12, 2)->default(0);
            $table->decimal('redondeo', 12, 2)->default(0);
            $table->unsignedBigInteger('apertura_id')->nullable();
            $table->string('estado', 20)->default('vigente');
            $table->string('idempotency_key')->nullable();
            $table->timestamps();
        });

        Schema::create('pagos_venta_detalle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pago_id');
            $table->string('metodo');
            $table->decimal('monto', 12, 2);
            $table->json('detalle')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_venta_detalle');
        Schema::dropIfExists('pagos_venta');
        Schema::dropIfExists('catalogo_metodos_pago');
        Schema::dropIfExists('caja_cierres');
        Schema::dropIfExists('caja_movimientos');
        Schema::dropIfExists('caja_aperturas');
    }
};
