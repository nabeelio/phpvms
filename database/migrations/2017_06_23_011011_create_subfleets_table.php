<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubfleetsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subfleets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('airline_id')->unsigned()->nullable();
            $table->string('name');
            $table->text('type');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subfleet_rank', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('subfleet_id')->unsigned()->nullable();
            $table->integer('rank_id')->unsigned()->nullable();
            $table->double('acars_pay', 19, 2)->unsigned()->nullable();
            $table->double('manual_pay', 19, 2)->unsigned()->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('subfleets');
        Schema::drop('subfleet_rank');
    }
}
