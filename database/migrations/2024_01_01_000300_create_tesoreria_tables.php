<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tes_cuentas_bancarias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('banco');
            $table->string('numero');
            $table->char('moneda',3)->default('USD');
            $table->string('alias')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('tes_estados_cuenta', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cuenta_bancaria_id');
            $table->string('periodo');
            $table->decimal('saldo_inicial',14,2)->default(0);
            $table->decimal('saldo_final',14,2)->default(0);
            $table->timestamps();
        });

        Schema::create('tes_estados_cuenta_lineas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('estado_id');
            $table->date('fecha');
            $table->string('descripcion');
            $table->string('referencia')->nullable();
            $table->decimal('monto',14,2);
            $table->enum('tipo',['credito','debito']);
            $table->boolean('conciliado')->default(false);
            $table->string('entidad_tipo')->nullable();
            $table->uuid('entidad_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tes_estados_cuenta_lineas');
        Schema::dropIfExists('tes_estados_cuenta');
        Schema::dropIfExists('tes_cuentas_bancarias');
    }
};
