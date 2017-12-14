<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAirportsTable extends Migration
{
    public function up()
    {
        Schema::create('airports', function (Blueprint $table) {
            $table->string('id', 5)->primary();
            $table->string('iata', 5)->nullable();
            $table->string('icao', 5);
            $table->string('name', 100);
            $table->string('location', 100)->nullable();
            $table->string('country', 64)->nullable();
            $table->string('tz', 64)->nullable();
            $table->unsignedDecimal('fuel_100ll_cost', 19)->default(0);
            $table->unsignedDecimal('fuel_jeta_cost', 19)->default(0);
            $table->unsignedDecimal('fuel_mogas_cost', 19)->default(0);
            $table->float('lat', 7, 4)->default(0.0)->nullable();
            $table->float('lon', 7, 4)->default(0.0)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('airports');
    }
}
