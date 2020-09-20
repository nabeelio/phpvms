<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add a `mtow` column for the max takeoff weight
 */
class AircraftAddMtow extends Migration
{
    public function up()
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->unsignedDecimal('mtow')
                ->nullable()
                ->default(0.0)
                ->after('hex_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->dropColumn('mtow');
        });
    }
}
