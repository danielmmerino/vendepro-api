<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100)->nullable();
            $table->unsignedInteger('capacidad');
            $table->enum('ubicacion', ['salon','terraza','barra','privado'])->default('salon')->index();
            $table->enum('estado', ['activa','inactiva'])->default('activa')->index();
            $table->string('notas', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};
