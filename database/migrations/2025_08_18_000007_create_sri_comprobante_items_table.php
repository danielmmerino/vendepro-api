<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sri_comprobante_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('comprobante_id')->index();
            $table->string('codigo_principal', 50);
            $table->string('descripcion', 300);
            $table->decimal('cantidad', 12, 4);
            $table->decimal('precio_unit', 12, 6);
            $table->decimal('descuento', 12, 6)->default(0);
            $table->enum('impuesto_codigo', ['2','3','5','6','7'])->nullable();
            $table->decimal('impuesto_tarifa', 5, 2)->nullable();
            $table->decimal('impuesto_base', 12, 6)->nullable();
            $table->decimal('impuesto_valor', 12, 6)->nullable();
            $table->unsignedInteger('orden');
            $table->timestamps();

            $table->foreign('comprobante_id')->references('id')->on('sri_comprobantes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sri_comprobante_items');
    }
};
