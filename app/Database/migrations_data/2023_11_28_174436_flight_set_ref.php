<?php

use App\Contracts\Migration;
use App\Models\Flight;
use Illuminate\Support\Facades\DB;

/**
 * Add the flight reference model to the current flights
 */
return new class() extends Migration {
    public function up()
    {
        DB::table('flights')->update(['ref_model' => Flight::class]);
    }
};
