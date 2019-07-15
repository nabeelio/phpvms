<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('image_url')->nullable();

            // ref fields are expenses tied to some model object
            // EG, the airports has an internal expense for gate costs
            $table->string('ref_model')->nullable();
            $table->text('ref_model_params')->nullable();
            //$table->string('ref_model_id', 36)->nullable();

            $table->timestamps();

            $table->index(['ref_model']);
        });

        Schema::create('user_awards', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('award_id');
            $table->timestamps();

            $table->index(['user_id', 'award_id']);
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
        Schema::dropIfExists('user_awards');
    }
}
