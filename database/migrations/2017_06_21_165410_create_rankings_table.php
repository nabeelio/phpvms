<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRankingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('hours')->default(0);
            $table->bool('auto_approval_acars')->default(false);
            $table->bool('auto_approval_manual')->default(false);
            $table->bool('auto_promote')->default(true);
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
        Schema::drop('rankings');
    }
}
