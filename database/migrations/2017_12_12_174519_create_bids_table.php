<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bids', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id', false, true);
            $table->uuid('flight_id');
            $table->timestamps();

            $table->index('user_id');
            $table->index(['user_id', 'flight_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_bids');
    }
}
