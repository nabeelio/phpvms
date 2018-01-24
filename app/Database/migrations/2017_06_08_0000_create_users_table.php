<?php

use App\Models\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('api_key', 40)->nullable();
            $table->unsignedInteger('airline_id');
            $table->unsignedInteger('rank_id')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('home_airport_id', 5)->nullable();
            $table->string('curr_airport_id', 5)->nullable();
            $table->string('last_pirep_id', 12)->nullable();
            $table->unsignedBigInteger('flights')->default(0);
            $table->unsignedBigInteger('flight_time')->nullable()->default(0);
            $table->decimal('balance', 19)->nullable();
            $table->string('timezone', 64)->nullable();
            $table->unsignedTinyInteger('status')->nullable()->default(0);
            $table->unsignedTinyInteger('state')->nullable()->default(0);
            $table->boolean('active')->nullable();
            $table->ipAddress('last_ip')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('email');
            $table->index('api_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
