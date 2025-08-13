<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario_saldos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bodega_id')->constrained('bodegas');
            $table->unsignedBigInteger('producto_id');
            $table->decimal('cantidad', 15, 4)->default(0);
            $table->decimal('costo_promedio', 15, 4)->default(0);
            $table->timestamps();
            $table->unique(['bodega_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_saldos');
    }
};
