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
            $table->boolean('auto_approve_acars')->default(false);
            $table->boolean('auto_approve_manual')->default(false);
            $table->boolean('auto_promote')->default(true);
            $table->timestamps();

            $table->unique('name');
        });

        Schema::create('aircraft_rank', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('aircraft_id')->unsigned();
            $table->integer('rank_id')->unsigned();
            $table->double('acars_pay', 19, 2)->default(0.0)->unsigned();
            $table->double('manual_pay', 19, 2)->default(0.0)->unsigned();
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
        Schema::drop('aircraft_rank');
    }
}
