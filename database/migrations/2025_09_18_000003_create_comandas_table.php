<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comandas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pedido_id')->constrained('pedidos');
            $table->foreignUuid('estacion_id')->constrained('kds_estaciones');
            $table->enum('estado', ['pendiente','en_preparacion','lista','servida'])->default('pendiente');
            $table->unsignedTinyInteger('curso')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamp('recall_at')->nullable();
            $table->unsignedInteger('sla_seg_objetivo')->nullable();
            $table->unsignedInteger('prep_time_seg')->nullable();
            $table->uuid('reassigned_from')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comandas');
    }
};
