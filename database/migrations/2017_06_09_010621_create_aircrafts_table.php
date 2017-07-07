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
            $table->string('hex_code')->nullable();
            $table->string('name');
            $table->string('registration')->nullable();
            $table->string('tail_number')->nullable();
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
