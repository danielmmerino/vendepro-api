<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('empresa_id');
            $table->string('identificacion', 20);
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('telefono', 100)->nullable();
            $table->string('email')->nullable();
            $table->timestamps();

            $table->unique(['empresa_id', 'identificacion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
