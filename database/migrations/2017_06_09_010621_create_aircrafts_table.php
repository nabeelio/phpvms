<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAircraftsTable extends Migration
{
    public function up()
    {
        Schema::create('aircraft', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aircraft_class_id')->unsigned()->nullable();
            $table->string('icao');
            $table->string('name');
            $table->string('registration')->nullable();
            $table->string('tail_number')->nullable();
            $table->double('cargo_capacity', 19, 2)->nullable();
            $table->double('fuel_capacity', 19, 2)->nullable();
            $table->double('gross_weight', 19, 2)->nullable();
            $table->tinyInteger('fuel_type')->unsigned()->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('icao');
            $table->unique('registration');
        });

    }

    public function down()
    {
        Schema::drop('aircraft');
    }
}
