<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFaresTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fares', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50);
            $table->string('name', 50);
            $table->decimal('price', 19, 2)->default(0.0);
            $table->decimal('cost', 19, 2)->default(0.0);
            $table->integer('capacity')->default(0)->unsigned();
            $table->string('notes')->nullable();
            $table->boolean('active')->default(true);
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
        Schema::drop('fares');
    }
}
