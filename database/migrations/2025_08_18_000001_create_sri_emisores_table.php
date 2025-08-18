<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sri_emisores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('ruc', 13)->unique();
            $table->string('razon_social', 200);
            $table->string('nombre_comercial', 200)->nullable();
            $table->string('contribuyente_especial', 10)->nullable();
            $table->boolean('obligado_contabilidad')->default(false);
            $table->string('direccion_matriz', 255);
            $table->enum('ambiente', ['1', '2'])->default('1');
            $table->enum('tipo_emision', ['1', '2'])->default('1');
            $table->string('email_contacto', 150)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sri_emisores');
    }
};
