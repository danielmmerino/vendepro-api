<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kds_sla_categoria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categoria_id');
            $table->unsignedInteger('sla_seg');
            $table->timestamps();
        });

        Schema::create('kds_sla_producto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id');
            $table->unsignedInteger('sla_seg');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kds_sla_producto');
        Schema::dropIfExists('kds_sla_categoria');
    }
};
