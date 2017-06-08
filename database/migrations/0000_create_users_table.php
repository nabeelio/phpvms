<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('code');
            $table->string('location');
            $table->string('hub');
            $table->unsignedBigInteger('flights');
            $table->float('hours');
            $table->float('pay');
            $table->boolean('confirmed');
            $table->boolean('retired');
            $table->dateTime('last_pirep');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
