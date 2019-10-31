<?php

use App\Contracts\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        // Change the column type to an unsigned small int (tinyint not supported on all)
        Schema::table('pireps', function (Blueprint $table) {
            $table->string('flight_id', Model::ID_MAX_LENGTH)->change();
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
