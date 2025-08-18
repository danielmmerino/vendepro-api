<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sri_establecimientos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('emisor_id');
            $table->char('codigo', 3);
            $table->string('direccion', 255);
            $table->string('nombre', 150)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['emisor_id', 'codigo']);
            $table->foreign('emisor_id')->references('id')->on('sri_emisores');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sri_establecimientos');
    }
};
