<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubfleetTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subfleets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('airline_id')->nullable();
            $table->string('name', 50);
            $table->string('type', 50);
            $table->unsignedTinyInteger('fuel_type')->nullable();
            $table->unsignedDecimal('ground_handling_multiplier')->nullable()->default(100);
            $table->unsignedDecimal('cargo_capacity')->nullable();
            $table->unsignedDecimal('fuel_capacity')->nullable();
            $table->unsignedDecimal('gross_weight')->nullable();
            $table->timestamps();
        });

        Schema::create('subfleet_expenses', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('subfleet_id');
            $table->string('name', 50);
            $table->unsignedDecimal('amount');
            $table->timestamps();

            $table->index('subfleet_id');
        });

        Schema::create('subfleet_fare', function (Blueprint $table) {
            $table->unsignedInteger('subfleet_id');
            $table->unsignedInteger('fare_id');
            $table->string('price')->nullable();
            $table->string('cost')->nullable();
            $table->string('capacity')->nullable();
            $table->timestamps();

            $table->primary(['subfleet_id', 'fare_id']);
            $table->index(['fare_id', 'subfleet_id']);
        });

        Schema::create('subfleet_flight', function(Blueprint $table) {
            $table->unsignedInteger('subfleet_id');
            $table->string('flight_id', 12);

            $table->primary(['subfleet_id', 'flight_id']);
            $table->index(['flight_id', 'subfleet_id']);
        });

        Schema::create('subfleet_rank', function(Blueprint $table) {
            $table->unsignedInteger('rank_id');
            $table->unsignedInteger('subfleet_id');
            $table->string('acars_pay')->nullable();
            $table->string('manual_pay')->nullable();

            $table->primary(['rank_id', 'subfleet_id']);
            $table->index(['subfleet_id', 'rank_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subfleets');
        Schema::dropIfExists('subfleet_expenses');
        Schema::dropIfExists('subfleet_fare');
        Schema::dropIfExists('subfleet_flight');
        Schema::dropIfExists('subfleet_rank');
    }
}
