<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_por_pagar', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('compra_id');
            $table->uuid('proveedor_id');
            $table->date('fecha_emision')->index();
            $table->date('fecha_vencimiento')->index();
            $table->decimal('total', 14, 2);
            $table->decimal('saldo_pendiente', 14, 2);
            $table->enum('estado', ['pendiente', 'pagada', 'anulada'])->default('pendiente');
            $table->timestamps();

            $table->foreign('compra_id')->references('id')->on('compras');
            $table->foreign('proveedor_id')->references('id')->on('proveedores');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_por_pagar');
    }
};
