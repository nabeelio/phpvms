<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubfleetsTable extends Migration
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
            $table->integer('airline_id')->unsigned()->nullable();
            $table->string('name', 50);
            $table->string('type', 7);
            $table->tinyInteger('fuel_type')->unsigned()->nullable();
            $table->double('cargo_capacity', 19, 2)->nullable();
            $table->double('fuel_capacity', 19, 2)->nullable();
            $table->double('gross_weight', 19, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('subfleet_expenses', function(Blueprint $table) {
            $table->integer('subfleet_id')->unsigned();
            $table->string('name', 50);
            $table->decimal('cost', 19, 2)->unsigned();

            $table->primary(['subfleet_id', 'name']);
        });

        Schema::create('subfleet_fare', function (Blueprint $table) {
            $table->integer('subfleet_id')->unsigned();
            $table->integer('fare_id')->unsigned();
            $table->decimal('price', 19, 2)->nullable();
            $table->decimal('cost', 19, 2)->nullable();
            $table->integer('capacity')->nullable()->unsigned();
            $table->timestamps();

            $table->primary(['subfleet_id', 'fare_id']);
            $table->index(['fare_id', 'subfleet_id']);
        });

        Schema::create('subfleet_flight', function(Blueprint $table) {
            $table->integer('subfleet_id')->unsigned();
            $table->integer('flight_id')->unsigned();

            $table->primary(['subfleet_id', 'flight_id']);
            $table->index(['flight_id', 'subfleet_id']);
        });

        Schema::create('subfleet_rank', function(Blueprint $table) {
            $table->integer('rank_id')->unsigned();
            $table->integer('subfleet_id')->unsigned();
            $table->double('acars_pay', 19, 2)->unsigned()->nullable();
            $table->double('manual_pay', 19, 2)->unsigned()->nullable();

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
        Schema::drop('subfleets');
        Schema::drop('subfleet_expenses');
        Schema::drop('subfleet_fare');
        Schema::drop('subfleet_flight');
        Schema::drop('subfleet_rank');
    }
}
