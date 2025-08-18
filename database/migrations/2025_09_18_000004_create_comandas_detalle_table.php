<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comandas_detalle', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('comanda_id')->constrained('comandas');
            $table->foreignUuid('item_id')->constrained('pedido_items');
            $table->decimal('cantidad',12,4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comandas_detalle');
    }
};
