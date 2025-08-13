<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('mesa_id');
            $table->timestamp('inicio');
            $table->timestamp('fin');
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada'])->default('pendiente')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
