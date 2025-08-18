<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promociones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo');
            $table->integer('prioridad')->default(0);
            $table->string('estado')->default('inactiva');
            $table->string('canal')->default('todos');
            $table->timestamp('vigencia_desde')->nullable();
            $table->timestamp('vigencia_hasta')->nullable();
            $table->json('dias_semana')->nullable();
            $table->time('hora_desde')->nullable();
            $table->time('hora_hasta')->nullable();
            $table->string('zona_horaria')->nullable();
            $table->json('condiciones')->nullable();
            $table->json('recompensa')->nullable();
            $table->json('limites')->nullable();
            $table->boolean('stackable')->default(true);
            $table->string('exclusividad_grupo')->nullable();
            $table->boolean('requiere_cupon')->default(false);
            $table->unsignedInteger('uso_global')->default(0);
            $table->boolean('uso_por_cliente_habilitado')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('promociones_usos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promocion_id')->constrained('promociones');
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('pedido_id')->nullable();
            $table->unsignedBigInteger('factura_id')->nullable();
            $table->decimal('monto_descuento', 10, 2);
            $table->timestamp('usado_at');
            $table->timestamps();
        });

        Schema::create('promociones_combo_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promocion_id')->constrained('promociones');
            $table->unsignedBigInteger('producto_id');
            $table->integer('cantidad');
            $table->timestamps();
        });

        Schema::create('cupones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained('promociones');
            $table->string('codigo')->unique();
            $table->timestamp('vigencia_hasta')->nullable();
            $table->unsignedInteger('uso_max_global')->nullable();
            $table->unsignedInteger('uso_max_por_cliente')->nullable();
            $table->string('estado')->default('activo');
            $table->unsignedInteger('usos_realizados')->default(0);
            $table->timestamps();
        });

        Schema::create('simulaciones', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->json('payload');
            $table->json('resultado')->nullable();
            $table->timestamp('vence_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pedidos_descuentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_id');
            $table->foreignId('promocion_id')->nullable()->constrained('promociones');
            $table->string('tipo');
            $table->decimal('base', 10, 2);
            $table->decimal('valor', 10, 2);
            $table->string('nivel');
            $table->unsignedBigInteger('linea_producto_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos_descuentos');
        Schema::dropIfExists('simulaciones');
        Schema::dropIfExists('cupones');
        Schema::dropIfExists('promociones_combo_detalle');
        Schema::dropIfExists('promociones_usos');
        Schema::dropIfExists('promociones');
    }
};
