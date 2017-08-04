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
            $table->uuid('id');
            $table->integer('airline_id')->unsigned();
            $table->string('flight_number', 10);
            $table->string('route_code', 5)->nullable();
            $table->string('route_leg', 5)->nullable();
            $table->integer('dpt_airport_id')->unsigned();
            $table->integer('arr_airport_id')->unsigned();
            $table->integer('alt_airport_id')->unsigned()->nullable();
            $table->text('route')->nullable();
            $table->text('dpt_time', 10)->nullable();
            $table->text('arr_time', 10)->nullable();
            $table->double('flight_time', 19, 2)->unsigned()->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->primary('id');

            $table->unique('flight_number');

            $table->index('flight_number');
            $table->index('dpt_airport_id');
            $table->index('arr_airport_id');
        });

        Schema::create('flight_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('flight_id');
            $table->string('name', 50);
            $table->text('value');
            $table->timestamps();
        });

        Schema::create('user_flights', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id', false, true);
            $table->uuid('flight_id');

            $table->index('user_id');
            $table->index(['user_id', 'flight_id']);
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
        Schema::drop('flight_fields');
        Schema::drop('user_flights');
    }
}
