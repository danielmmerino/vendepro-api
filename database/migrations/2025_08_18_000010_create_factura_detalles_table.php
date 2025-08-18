<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('factura_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas');
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->string('descripcion');
            $table->decimal('cantidad',10,2);
            $table->decimal('precio_unitario',10,2);
            $table->decimal('descuento',10,2)->default(0);
            $table->string('impuesto_codigo')->nullable();
            $table->decimal('impuesto_tarifa',5,2)->nullable();
            $table->decimal('iva_monto',10,2)->default(0);
            $table->decimal('total_linea',10,2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_detalles');
    }
};
