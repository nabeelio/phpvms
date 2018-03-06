<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('airline_id')->nullable();

            $table->string('name');
            $table->unsignedInteger('amount');
            $table->unsignedTinyInteger('type');
            $table->boolean('multiplier')->nullable()->default(0);
            $table->boolean('active')->nullable()->default(true);

            # ref fields are expenses tied to some model object
            # EG, the airports has an internal expense for gate costs
            $table->string('ref_class')->nullable();
            $table->string('ref_class_id', 36)->nullable();
            $table->index(['ref_class', 'ref_class_id']);

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
        Schema::dropIfExists('expenses');
    }
}
