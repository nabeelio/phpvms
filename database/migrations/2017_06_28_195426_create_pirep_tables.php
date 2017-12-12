<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePirepTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pireps', function (Blueprint $table) {
            $table->uuid('id');
            $table->integer('user_id')->unsigned();
            $table->integer('airline_id')->unsigned();
            $table->integer('aircraft_id')->nullable();
            $table->uuid('flight_id')->nullable();
            $table->string('flight_number', 10)->nullable();
            $table->string('route_code', 5)->nullable();
            $table->string('route_leg', 5)->nullable();
            $table->string('dpt_airport_id', 5);
            $table->string('arr_airport_id', 5);
            $table->double('flight_time', 19, 2)->unsigned();
            $table->double('gross_weight', 19, 2)->nullable();
            $table->double('fuel_used', 19, 2)->nullable();
            $table->string('route')->nullable();
            $table->string('notes')->nullable();
            $table->tinyInteger('source')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->longText('raw_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
            $table->index('user_id');
            $table->index('flight_id');
            $table->index('dpt_airport_id');
            $table->index('arr_airport_id');
        });

        Schema::create('pirep_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('pirep_id');
            $table->bigInteger('user_id', false, true);
            $table->text('comment');
            $table->timestamps();
        });

        Schema::create('pirep_events', function(Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->uuid('pirep_id');
            $table->string('event');
            $table->dateTime('dt');
        });

        /*
         * Financial tables/fields
         */
        Schema::create('pirep_expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('pirep_id');
            $table->string('name');
            $table->double('value', 19, 2)->nullable();

            $table->index('pirep_id');
        });

        Schema::create('pirep_fares', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('pirep_id');
            $table->unsignedBigInteger('fare_id');
            $table->double('count', 19, 2)->nullable();

            $table->index('pirep_id');
        });

        /*
         * Additional PIREP data
         */
        Schema::create('pirep_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->integer('required');
            $table->timestamps();
        });

        Schema::create('pirep_field_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('pirep_id');
            $table->string('name', 50);
            $table->text('value');
            $table->string('source')->nullable();
            $table->timestamps();

            $table->index('pirep_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pireps');
        Schema::dropIfExists('pirep_comments');
        Schema::dropIfExists('pirep_expenses');
        Schema::dropIfExists('pirep_fares');
        Schema::dropIfExists('pirep_fields');
        Schema::dropIfExists('pirep_field_values');
    }
}
