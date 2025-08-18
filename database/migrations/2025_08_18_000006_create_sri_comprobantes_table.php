<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sri_comprobantes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('tipo', ['01','04','05','06','07']);
            $table->uuid('emisor_id');
            $table->char('establecimiento', 3);
            $table->char('punto_emision', 3);
            $table->unsignedInteger('secuencial');
            $table->string('numero', 17)->index();
            $table->date('fecha_emision');
            $table->char('clave_acceso', 49)->unique();
            $table->enum('ambiente', ['1','2']);
            $table->enum('tipo_emision', ['1','2']);
            $table->enum('estado', ['generado','firmado','enviado','autorizado','no_autorizado','rechazado','error','anulado'])->default('generado')->index();
            $table->enum('fuente', ['factura_venta','nota_credito_venta','nota_debito_venta','guia_remision','retencion','manual'])->default('manual');
            $table->uuid('fuente_id')->nullable();
            $table->string('receptor_identificacion', 20);
            $table->string('receptor_razon_social', 200);
            $table->decimal('total_sin_impuestos', 14, 2);
            $table->decimal('total_descuento', 14, 2)->default(0);
            $table->decimal('propina', 14, 2)->default(0);
            $table->decimal('importe_total', 14, 2);
            $table->char('moneda', 3)->default('USD');
            $table->longText('xml_generado')->nullable();
            $table->longText('xml_firmado')->nullable();
            $table->string('nro_autorizacion', 49)->nullable();
            $table->dateTime('fecha_autorizacion')->nullable();
            $table->string('ride_pdf_path', 255)->nullable();
            $table->text('ultimo_error')->nullable();
            $table->uuid('certificado_id')->nullable();
            $table->unsignedInteger('reintentos_envio')->default(0);
            $table->unsignedInteger('reintentos_autorizacion')->default(0);
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['clave_acceso', 'estado', 'fecha_emision']);
            $table->foreign('emisor_id')->references('id')->on('sri_emisores');
            $table->foreign('certificado_id')->references('id')->on('sri_certificados');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sri_comprobantes');
    }
};
