<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('mesa_id')->nullable();
            $table->string('cliente_nombre',120)->nullable();
            $table->enum('estado', ['abierto','enviado','preparando','listo','servido','cerrado','anulado'])->default('abierto')->index();
            $table->enum('origen', ['salon','para_llevar','delivery'])->default('salon');
            $table->string('notas',255)->nullable();
            $table->decimal('subtotal',14,2)->default(0);
            $table->decimal('descuento',14,2)->default(0);
            $table->decimal('impuesto',14,2)->default(0);
            $table->decimal('total',14,2)->default(0);
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
