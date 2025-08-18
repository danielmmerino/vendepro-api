<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->unsignedBigInteger('local_id')->nullable();
            $table->tinyInteger('ambiente');
            $table->char('establecimiento',3);
            $table->char('punto_emision',3);
            $table->unsignedBigInteger('secuencial')->nullable();
            $table->string('numero')->nullable();
            $table->date('fecha_emision');
            $table->string('estado')->default('borrador');
            $table->string('clave_acceso')->nullable();
            $table->string('autorizacion_numero')->nullable();
            $table->timestamp('autorizado_at')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->string('cliente_identificacion')->nullable();
            $table->string('cliente_tipo')->nullable();
            $table->string('cliente_razon_social')->nullable();
            $table->string('cliente_email')->nullable();
            $table->string('cliente_direccion')->nullable();
            $table->decimal('subtotal_0',10,2)->default(0);
            $table->decimal('subtotal_12',10,2)->default(0);
            $table->decimal('subtotal_15',10,2)->default(0);
            $table->decimal('subtotal_exento',10,2)->default(0);
            $table->decimal('subtotal_no_objeto',10,2)->default(0);
            $table->decimal('descuento_total',10,2)->default(0);
            $table->decimal('propina',10,2)->default(0);
            $table->decimal('iva_total',10,2)->default(0);
            $table->decimal('total',10,2)->default(0);
            $table->text('observacion')->nullable();
            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
