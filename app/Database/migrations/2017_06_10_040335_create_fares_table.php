<?php

use App\Interfaces\Migration;
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
            $table->string('code', 50)->unique();
            $table->string('name', 50);
            $table->unsignedDecimal('price')->nullable()->default(0.00);
            $table->unsignedDecimal('cost')->nullable()->default(0.00);
            $table->unsignedInteger('capacity')->nullable()->default(0);
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
        Schema::dropIfExists('fares');
    }
}
