<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pedido_id')->index();
            $table->string('nombre',60)->default('Cuenta');
            $table->string('notas',255)->nullable();
            $table->decimal('subtotal',14,2)->default(0);
            $table->decimal('descuento',14,2)->default(0);
            $table->decimal('impuesto',14,2)->default(0);
            $table->decimal('total',14,2)->default(0);
            $table->enum('estado',["abierta","cerrada","anulada"])->default('abierta')->index();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['pedido_id','estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
