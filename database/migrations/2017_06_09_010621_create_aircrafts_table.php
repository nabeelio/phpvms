<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAircraftsTable extends Migration
{
    public function up()
    {
        Schema::create('aircraft', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subfleet_id')->unsigned();
            $table->integer('airport_id')->unsigned()->nullable();
            $table->string('hex_code', 10)->nullable();
            $table->string('name', 50);
            $table->string('registration', 10)->nullable();
            $table->string('tail_number', 10)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique('registration');
        });

    }

    public function down()
    {
        Schema::drop('aircraft');
    }
}
