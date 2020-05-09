<?php

use App\Models\Enums\FareType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FaresAddType extends Migration
{
    /**
     * Add a `pilot_pay` column for a fixed amount to pay to a pilot for a flight
     */
    public function up()
    {
        Schema::table('fares', function (Blueprint $table) {
            $table->unsignedTinyInteger('type')
                ->default(FareType::PASSENGER)
                ->nullable()
                ->after('capacity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fares', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
