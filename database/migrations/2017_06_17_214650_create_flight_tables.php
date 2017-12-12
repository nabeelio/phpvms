<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFlightTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->string('id', 12);
            $table->unsignedInteger('airline_id');
            $table->string('flight_number', 10);
            $table->string('route_code', 5)->nullable();
            $table->string('route_leg', 5)->nullable();
            $table->string('dpt_airport_id', 5);
            $table->string('arr_airport_id', 5);
            $table->string('alt_airport_id', 5)->nullable();
            $table->text('route')->nullable();
            $table->string('dpt_time', 10)->nullable();
            $table->string('arr_time', 10)->nullable();
            $table->unsignedDecimal('flight_time', 19)->nullable();
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
            $table->increments('id');
            $table->string('flight_id', 12);
            $table->string('name', 50);
            $table->text('value');
            $table->timestamps();

            $table->index('flight_id');
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
    }
}
