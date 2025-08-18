<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sri_secuencias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('punto_emision_id');
            $table->enum('tipo', ['01','04','05','06','07']);
            $table->unsignedInteger('actual');
            $table->boolean('bloqueado')->default(false);
            $table->timestamps();

            $table->unique(['punto_emision_id', 'tipo']);
            $table->foreign('punto_emision_id')->references('id')->on('sri_puntos_emision');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sri_secuencias');
    }
};
