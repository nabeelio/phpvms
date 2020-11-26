<?php

use App\Contracts\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Allow the flight field value to be nullable
 */
class FlightFieldNullable extends Migration
{
    public function up()
    {
        Schema::table('flight_field_values', function ($table) {
            $table->text('value')->change()->nullable();
        });
    }
}
