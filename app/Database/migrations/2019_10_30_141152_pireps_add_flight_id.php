<?php

use App\Contracts\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PirepsAddFlightId extends Migration
{
    /**
     * Add a `flight_id` column to the PIREPs table
     */
    public function up()
    {
        Schema::table('pireps', function (Blueprint $table) {
            $table->string('flight_id', Model::ID_MAX_LENGTH)->nullable()->after('aircraft_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pireps', function (Blueprint $table) {
            $table->dropColumn('flight_id');
        });
    }
}
