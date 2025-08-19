<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_proveedor', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cxp_id');
            $table->date('fecha_pago');
            $table->decimal('monto', 14, 2);
            $table->string('forma_pago', 50);
            $table->string('referencia', 100)->nullable();
            $table->uuid('usuario_id')->nullable();
            $table->timestamps();

            $table->foreign('cxp_id')->references('id')->on('cuentas_por_pagar');
            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_proveedor');
    }
};
