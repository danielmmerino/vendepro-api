<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kds_estaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('local_id');
            $table->string('nombre',150);
            $table->json('categorias')->nullable();
            $table->json('productos_override')->nullable();
            $table->unsignedBigInteger('impresora_id')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('sonido_alertas')->default(true);
            $table->string('color_acento',20)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kds_estaciones');
    }
};
