<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AirlineRemoveNullable extends Migration
{
    public function up()
    {
        Schema::table('airlines', function (Blueprint $table) {
            $table->dropUnique('airlines_iata_unique');
        });
    }

    public function down()
    {
    }
}
