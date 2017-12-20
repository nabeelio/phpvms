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
        Schema::create('navdata', function (Blueprint $table) {
            $table->string('id', 4);
            $table->string('name', 24);
            $table->unsignedInteger('type');
            $table->float('lat', 7, 4)->default(0.0);
            $table->float('lon', 7, 4)->default(0.0);
            $table->string('freq', 7)->nullable();

            $table->index('id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navdata');
    }
}
