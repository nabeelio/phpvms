<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePirepsTable extends Migration
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
            $table->integer('user_id');
            $table->string('flight_id');
            $table->integer('aircraft_id');
            $table->text('route_code')->nullable();
            $table->text('route_leg')->nullable();
            $table->integer('dpt_airport_id')->unsigned();
            $table->integer('arr_airport_id')->unsigned();
            $table->integer('flight_time')->unsigned();
            $table->integer('level')->unsigned();
            $table->string('route')->nullable();
            $table->string('notes')->nullable();
            $table->tinyInteger('status');
            $table->string('raw_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
            $table->index('user_id');
            $table->index('flight_id');
            $table->index('dpt_airport_id');
            $table->index('arr_airport_id');
        });

        Schema::create('pirep_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('required');
            $table->timestamps();
        });

        Schema::create('pirep_field_values', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('pirep_id');
            $table->string('name');
            $table->string('value');
            $table->tinyInteger('source')->default(0);
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
        Schema::drop('pireps');
        Schema::drop('pirep_fields');
        Schema::drop('pirep_field_values');
    }
}
