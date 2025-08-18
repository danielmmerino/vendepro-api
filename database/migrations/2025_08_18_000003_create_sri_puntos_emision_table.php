<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sri_puntos_emision', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('establecimiento_id');
            $table->char('codigo', 3);
            $table->unsignedInteger('sec_hasta')->default(999999999);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['establecimiento_id', 'codigo']);
            $table->foreign('establecimiento_id')->references('id')->on('sri_establecimientos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sri_puntos_emision');
    }
};
