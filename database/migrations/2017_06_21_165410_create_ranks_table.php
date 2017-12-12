<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRanksTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ranks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->unsignedInteger('hours')->default(0);
            $table->boolean('auto_approve_acars')->default(false);
            $table->boolean('auto_approve_manual')->default(false);
            $table->boolean('auto_promote')->default(true);
            $table->timestamps();

            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ranks');
    }
}
