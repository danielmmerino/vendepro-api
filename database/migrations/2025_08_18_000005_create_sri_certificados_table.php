<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sri_certificados', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('emisor_id');
            $table->string('alias', 120);
            $table->longText('p12_base64');
            $table->string('p12_password', 200);
            $table->date('valido_desde')->nullable();
            $table->date('valido_hasta')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('emisor_id')->references('id')->on('sri_emisores');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sri_certificados');
    }
};
