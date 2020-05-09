<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FlightsAddPilotPay extends Migration
{
    /**
     * Add a `pilot_pay` column for a fixed amount to pay to a pilot for a flight
     */
    public function up()
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->decimal('pilot_pay')
                ->nullable()
                ->after('route');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropColumn('pilot_pay');
        });
    }
}
