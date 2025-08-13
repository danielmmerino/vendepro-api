<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('mesa_id');
            $table->string('cliente_nombre', 120);
            $table->string('cliente_telefono', 40)->nullable();
            $table->string('cliente_email', 120)->nullable();
            $table->unsignedInteger('comensales');
            $table->dateTime('inicio')->index();
            $table->dateTime('fin')->index();
            $table->enum('estado', ['pendiente','confirmada','cancelada','no_show'])->default('pendiente')->index();
            $table->enum('canal', ['telefono','web','app','walk_in','tercero'])->default('telefono');
            $table->string('notas', 255)->nullable();
            $table->string('idempotency_key', 80)->nullable()->unique();
            $table->uuid('usuario_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['mesa_id','inicio','fin','estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
