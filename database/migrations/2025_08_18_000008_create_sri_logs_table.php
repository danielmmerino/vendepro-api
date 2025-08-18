<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sri_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('comprobante_id')->index();
            $table->enum('fase', ['generar','firmar','enviar','autorizar','reenviar','consultar']);
            $table->longText('request_payload')->nullable();
            $table->longText('response_payload')->nullable();
            $table->integer('http_code')->nullable();
            $table->string('mensaje', 300)->nullable();
            $table->text('error')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('comprobante_id')->references('id')->on('sri_comprobantes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sri_logs');
    }
};
