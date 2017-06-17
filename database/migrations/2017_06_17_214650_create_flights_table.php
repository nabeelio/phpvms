<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFlightsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('airline_id')->unsigned();
            $table->text('flight_number');
            $table->text('route_code');
            $table->text('route_leg');
            $table->integer('dpt_airport_id')->unsigned();
            $table->integer('arr_airport_id')->unsigned();
            $table->integer('alt_airport_id')->unsigned();
            $table->text('route');
            $table->text('dpt_time');
            $table->text('arr_time');
            $table->text('notes');
            $table->boolean('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('flight_aircraft', function ($table) {
            $table->increments('id');
            $table->integer('flight_id')->unsigned();
            $table->integer('aircraft_id')->unsigned();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('flights');
        Schema::drop('flight_aircraft');
    }
}
