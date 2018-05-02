<?php

use App\Interfaces\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('id', 12);
            $table->string('pirep_id', \App\Interfaces\Model::ID_MAX_LENGTH);
            $table->unsignedTinyInteger('type');
            $table->unsignedInteger('nav_type')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->string('name')->nullable();
            $table->string('log')->nullable();
            $table->float('lat', 7, 4)->nullable()->default(0.0);
            $table->float('lon', 7, 4)->nullable()->default(0.0);
            $table->unsignedInteger('heading')->nullable();
            $table->unsignedInteger('altitude')->nullable();
            $table->integer('vs')->nullable();
            $table->unsignedInteger('gs')->nullable();
            $table->unsignedInteger('transponder')->nullable();
            $table->string('autopilot')->nullable();
            $table->decimal('fuel')->nullable();
            $table->decimal('fuel_flow')->nullable();
            $table->string('sim_time')->nullable();

            $table->timestamps();

            $table->primary('id');
            $table->index('pirep_id');
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
