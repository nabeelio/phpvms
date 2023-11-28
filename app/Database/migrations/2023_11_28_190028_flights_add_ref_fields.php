<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        Schema::table('flights', function (Blueprint $table) {
            // ref fields are flights tied to some model object
            $table->string('ref_model')->nullable();
            $table->string('ref_model_id', 36)->nullable();

            $table->index(['ref_model', 'ref_model_id']);
        });
    }
};
