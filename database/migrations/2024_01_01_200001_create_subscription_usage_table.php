<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscription_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('suscripciones')->onDelete('cascade');
            $table->string('metric');
            $table->decimal('valor', 12, 2)->default(0);
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->timestamps();
            $table->unique(['subscription_id', 'metric', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_usage');
    }
};
