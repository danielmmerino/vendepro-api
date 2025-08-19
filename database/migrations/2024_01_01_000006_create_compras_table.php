<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('proveedor_id');
            $table->date('fecha')->index();
            $table->string('numero_factura', 50);
            $table->decimal('subtotal', 14, 2);
            $table->decimal('descuento', 14, 2)->default(0);
            $table->decimal('impuesto', 14, 2)->default(0);
            $table->decimal('total', 14, 2);
            $table->enum('estado', ['borrador', 'aprobada', 'anulada'])->default('borrador');
            $table->string('observacion', 255)->nullable();
            $table->uuid('usuario_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('proveedor_id')->references('id')->on('proveedores');
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->unique(['proveedor_id', 'numero_factura']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
