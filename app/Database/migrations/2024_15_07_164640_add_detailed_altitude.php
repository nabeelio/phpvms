<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('acars', function (Blueprint $table) {
            $table->decimal('altitude_agl')->nullable()->default(0.0)->after('altitude');
            $table->decimal('altitude_msl')->nullable()->default(0.0)->after('altitude_msl');
        });
    }

    public function down(): void
    {
        Schema::dropColumns('acars', ['altitude_agl', 'altitude_msl']);
    }
};
