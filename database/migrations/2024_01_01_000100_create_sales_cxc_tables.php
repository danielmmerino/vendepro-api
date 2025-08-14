<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('facturas_venta', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pedido_id')->nullable();
            $table->uuid('cliente_id')->nullable();
            $table->uuid('cuenta_id')->nullable();
            $table->string('numero', 40)->unique();
            $table->date('fecha')->index();
            $table->decimal('subtotal', 14, 2);
            $table->decimal('descuento', 14, 2)->default(0);
            $table->decimal('impuesto', 14, 2)->default(0);
            $table->decimal('total', 14, 2);
            $table->char('moneda', 3)->default('USD');
            $table->enum('estado', ['borrador','aprobada','anulada'])->default('borrador');
            $table->enum('canal', ['salon','para_llevar','delivery','mostrador'])->default('mostrador');
            $table->string('notas',255)->nullable();
            $table->uuid('usuario_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['numero','fecha','estado']);
        });

        Schema::create('factura_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('factura_id');
            $table->uuid('pedido_item_id')->nullable();
            $table->uuid('producto_id');
            $table->string('concepto',160);
            $table->decimal('cantidad',12,4);
            $table->decimal('precio_unit',12,2);
            $table->decimal('impuesto_porcentaje',5,2)->default(0);
            $table->decimal('total_linea',14,2);
            $table->string('notas',255)->nullable();
            $table->timestamps();
            $table->index(['factura_id','producto_id']);
        });

        Schema::create('cxc_documentos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('factura_id')->unique();
            $table->uuid('cliente_id');
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->decimal('total',14,2);
            $table->decimal('saldo_pendiente',14,2);
            $table->enum('estado',['pendiente','pagada','anulada'])->default('pendiente');
            $table->timestamps();
            $table->index(['cliente_id','estado','fecha_emision']);
        });

        Schema::create('cxc_pagos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cxc_id');
            $table->date('fecha_pago');
            $table->decimal('monto',14,2);
            $table->enum('forma_pago',['efectivo','tarjeta','transferencia','otros'])->default('efectivo');
            $table->string('referencia',100)->nullable();
            $table->uuid('usuario_id')->nullable();
            $table->timestamps();
            $table->index(['cxc_id','fecha_pago']);
        });

        Schema::create('cxc_notas_credito', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('factura_id');
            $table->string('numero',40)->unique();
            $table->date('fecha')->index();
            $table->string('motivo',160);
            $table->decimal('subtotal',14,2);
            $table->decimal('impuesto',14,2);
            $table->decimal('total',14,2);
            $table->enum('estado',['emitida','aplicada','anulada'])->default('emitida');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['factura_id','numero','estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cxc_notas_credito');
        Schema::dropIfExists('cxc_pagos');
        Schema::dropIfExists('cxc_documentos');
        Schema::dropIfExists('factura_items');
        Schema::dropIfExists('facturas_venta');
    }
};
