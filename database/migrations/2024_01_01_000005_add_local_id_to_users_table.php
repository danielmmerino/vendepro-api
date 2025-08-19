<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('usuarios') && !Schema::hasColumn('usuarios', 'local_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->unsignedBigInteger('local_id')->nullable()->after('token_version');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('usuarios') && Schema::hasColumn('usuarios', 'local_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('local_id');
            });
        }
    }
};
