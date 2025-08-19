<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('usuarios') && !Schema::hasColumn('usuarios', 'token_version')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->unsignedInteger('token_version')->default(0)->after('password_hash');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('usuarios') && Schema::hasColumn('usuarios', 'token_version')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('token_version');
            });
        }
    }
};
