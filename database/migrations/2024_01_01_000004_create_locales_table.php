<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nombre', 120);
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('codigo_establecimiento', 3);
            $table->string('codigo_punto_emision', 3);
            $table->integer('secuencial_factura')->default(1);
            $table->integer('secuencial_nc')->default(1);
            $table->integer('secuencial_retencion')->default(1);
            $table->boolean('activo')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['empresa_id', 'codigo_establecimiento', 'codigo_punto_emision'], 'unq_local_sri');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locales');
    }
};
