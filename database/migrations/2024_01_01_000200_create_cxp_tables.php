<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cuentas_por_pagar', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('compra_id')->nullable();
            $table->uuid('proveedor_id');
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->decimal('total',14,2);
            $table->decimal('saldo_pendiente',14,2);
            $table->enum('estado',["pendiente","pagada","anulada"])->default('pendiente');
            $table->timestamps();
            $table->index(['proveedor_id','estado','fecha_emision']);
        });

        Schema::create('pagos_proveedor', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cxp_id');
            $table->date('fecha_pago');
            $table->decimal('monto',14,2);
            $table->string('forma_pago',50);
            $table->string('referencia',100)->nullable();
            $table->timestamps();
            $table->index(['cxp_id','fecha_pago']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_proveedor');
        Schema::dropIfExists('cuentas_por_pagar');
    }
};
