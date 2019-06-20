<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReadonlyToRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('roles', static function (Blueprint $table) {
            $table->boolean('read_only');
        });

        // Set the two main roles as read-only
        DB::table('roles')
            ->whereIn('name', ['admin', 'user'])
            ->update(['read_only' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('roles', static function (Blueprint $table) {
            $table->dropColumn('read_only');
        });
    }
}
