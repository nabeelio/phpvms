<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Remove the unique index from subfleets.type
 */
class RemoveSubfleetTypeIndex extends Migration
{
    public function up()
    {
        Schema::table('subfleets', function (Blueprint $table) {
            $table->dropUnique(['type']);
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
