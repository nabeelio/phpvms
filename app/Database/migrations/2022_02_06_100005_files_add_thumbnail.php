<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FilesAddThumbnail extends Migration
{
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('path');
        });
    }

    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
           $table->dropColumn('thumbnail');
        });
    }
}
