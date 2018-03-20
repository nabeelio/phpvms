<?php

use App\Models\Enums\FlightType;
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
            $table->string('id', \App\Models\Flight::ID_MAX_LENGTH);
            $table->unsignedInteger('airline_id');
            $table->string('flight_number', 10);
            $table->string('route_code', 5)->nullable();
            $table->string('route_leg', 5)->nullable();
            $table->string('dpt_airport_id', 5);
            $table->string('arr_airport_id', 5);
            $table->string('alt_airport_id', 5)->nullable();
            $table->string('dpt_time', 10)->nullable();
            $table->string('arr_time', 10)->nullable();
            $table->unsignedInteger('level')->nullable()->default(0);
            $table->unsignedDecimal('distance')->nullable()->default(0.0);
            $table->unsignedInteger('flight_time')->nullable();
            $table->tinyInteger('flight_type')->default(FlightType::PASSENGER);
            $table->text('route')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('has_bid')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->primary('id');

            $table->index('flight_number');
            $table->index('dpt_airport_id');
            $table->index('arr_airport_id');
        });

        Schema::create('flight_fare', function (Blueprint $table) {
            $table->string('flight_id', \App\Models\Flight::ID_MAX_LENGTH);
            $table->unsignedInteger('fare_id');
            $table->string('price', 10)->nullable();
            $table->string('cost', 10)->nullable();
            $table->string('capacity', 10)->nullable();
            $table->timestamps();

            $table->primary(['flight_id', 'fare_id']);
        });

        Schema::create('flight_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('flight_id', \App\Models\Flight::ID_MAX_LENGTH);
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
        Schema::drop('flight_fields');
        Schema::drop('flight_fare');
        Schema::drop('flights');
    }
}
