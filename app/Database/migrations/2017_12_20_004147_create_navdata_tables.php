<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNavdataTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * See for defs, modify/update based on this
         * https://github.com/skiselkov/openfmc/blob/master/airac.h
         */
        Schema::create('navpoints', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 10);
            $table->string('title', 25);
            $table->string('airway', 7)->nullable();
            $table->string('airway_type', 1)->nullable();
            $table->bigInteger('seq')->nullable();
            $table->string('loc', 4)->nullable();
            $table->float('lat', 7, 4)->default(0.0);
            $table->float('lon', 7, 4)->default(0.0);
            $table->string('freq', 7);
            $table->integer('type');

            $table->index('name');
            $table->index('airway');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navpoints');
    }
}
