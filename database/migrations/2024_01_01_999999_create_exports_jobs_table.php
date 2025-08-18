<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exports_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('reporte');
            $table->string('formato');
            $table->json('filtros_json')->nullable();
            $table->string('estado')->default('procesando');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exports_jobs');
    }
};
