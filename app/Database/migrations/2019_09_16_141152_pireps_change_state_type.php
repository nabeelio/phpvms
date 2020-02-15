<?php

use App\Models\Enums\PirepState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PirepsChangeStateType extends Migration
{
    /**
     * Change the PIREP state column to be a TINYINT
     *
     * @return void
     */
    public function up()
    {
        // Migrate the old rejected state
        DB::table('pireps')
            ->where(['state' => -1])
            ->update(['state' => PirepState::REJECTED]);

        // Change the column type to an unsigned small int (tinyint not supported on all)
        Schema::table('pireps', function (Blueprint $table) {
            $table->unsignedSmallInteger('state')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
