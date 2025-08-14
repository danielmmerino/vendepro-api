<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cuenta_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cuenta_id')->index();
            $table->foreignUuid('pedido_item_id')->index();
            $table->decimal('cantidad',12,4);
            $table->decimal('monto',12,2);
            $table->decimal('impuesto_monto',12,2)->default(0);
            $table->string('notas',255)->nullable();
            $table->timestamps();
            $table->unique(['cuenta_id','pedido_item_id']);
            $table->index(['pedido_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_items');
    }
};
