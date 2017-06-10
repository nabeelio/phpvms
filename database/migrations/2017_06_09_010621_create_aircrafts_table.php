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
            $table->string('full_name')->nullable();
            $table->string('registration')->nullable();
            $table->string('tail_number')->nullable();
            $table->string('cargo_capacity')->nullable();
            $table->string('fuel_capacity')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('icao');
            $table->unique('registration');
        });

        Schema::create('aircraft_classes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
        });
    }

    public function down()
    {
        Schema::drop('aircraft');
        Schema::drop('aircraft_classes');
    }
}
