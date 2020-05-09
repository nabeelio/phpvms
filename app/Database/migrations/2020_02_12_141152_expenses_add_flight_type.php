<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExpensesAddFlightType extends Migration
{
    /**
     * Add a `flight_type` column to the expenses table
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('flight_type', 50)
                ->nullable()
                ->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('flight_type');
        });
    }
}
