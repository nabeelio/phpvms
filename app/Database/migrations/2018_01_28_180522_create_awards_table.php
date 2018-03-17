<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('awards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50);
            $table->text('description')->nullable();
            $table->text('image_url')->nullable();

            # ref fields are expenses tied to some model object
            # EG, the airports has an internal expense for gate costs
            $table->string('ref_class')->nullable();
            $table->string('ref_class_id', 36)->nullable();

            $table->timestamps();

            $table->index(['ref_class', 'ref_class_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('awards');
    }
}
