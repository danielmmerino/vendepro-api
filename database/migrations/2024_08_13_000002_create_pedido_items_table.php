<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pedido_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pedido_id')->constrained('pedidos');
            $table->uuid('producto_id');
            $table->string('nombre',150);
            $table->decimal('cantidad',12,4);
            $table->decimal('precio_unit',12,2);
            $table->decimal('impuesto_porcentaje',5,2)->default(0);
            $table->string('notas',255)->nullable();
            $table->enum('estado', ['pendiente','en_cola','en_proceso','listo','servido','anulado'])->default('pendiente')->index();
            $table->enum('estacion', ['cocina','bar','postres','otros'])->index();
            $table->unsignedBigInteger('orden_sec');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['pedido_id','estado','estacion','orden_sec']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_items');
    }
};
