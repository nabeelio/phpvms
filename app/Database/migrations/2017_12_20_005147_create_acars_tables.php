<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcarsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pirep_id', 12);
            $table->string('name', 10)->nullable();
            $table->float('lat', 7, 4)->default(0.0);
            $table->float('lon', 7, 4)->default(0.0);

            # TODO: More columns here for what might be required

            # polymorphic relation columns.
            # parent_type can be flight, pirep or acars
            # once
            #$table->unsignedBigInteger('parent_id');
            #$table->string('parent_type');

            $table->timestamps();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acars');
    }
}
