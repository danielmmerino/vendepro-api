<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clientes_kpis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id')->unique();
            $table->decimal('total_compras', 12, 2)->default(0);
            $table->integer('tickets')->default(0);
            $table->decimal('ticket_promedio', 12, 2)->default(0);
            $table->date('ultima_compra')->nullable();
            $table->unsignedBigInteger('categoria_top_id')->nullable();
            $table->unsignedBigInteger('producto_top_id')->nullable();
            $table->json('rfm_json')->nullable();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes_kpis');
    }
};
