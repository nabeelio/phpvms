<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFaresTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fares', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->float('price');
            $table->float('cost')->default(0.0);
            $table->integer('capacity')->default(0);
            $table->string('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('aircraft_fare', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aircraft_id');
            $table->integer('fare_id');
            $table->float('price')->nullable();
            $table->float('cost')->nullable();
            $table->float('capacity')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('fares');
        Schema::drop('aircraft_fare');
    }
}
