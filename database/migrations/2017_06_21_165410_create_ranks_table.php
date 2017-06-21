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
            $table->string('name');
            $table->integer('hours')->default(0);
            $table->boolean('auto_approval_acars')->default(false);
            $table->boolean('auto_approval_manual')->default(false);
            $table->boolean('auto_promote')->default(true);
            $table->timestamps();
        });

        Schema::create('flight_rank', function(Blueprint $table) {
           $table->increments('id');
           $table->integer('flight_id')->unsigned();
           $table->integer('rank_id')->unsigned();
           $table->double('manual_pay', 19, 2)->default(0.0)->unsigned();
           $table->double('acars_pay', 19, 2)->default(0.0)->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ranks');
        Schema::drop('flight_rank');
    }
}
