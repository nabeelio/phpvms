<?php

use App\Models\Enums\FlightType;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePirepTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pireps', function (Blueprint $table) {
            $table->string('id', \App\Interfaces\Model::ID_MAX_LENGTH);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('airline_id');
            $table->unsignedInteger('aircraft_id')->nullable();
            $table->string('flight_number', 10)->nullable();
            $table->string('route_code', 5)->nullable();
            $table->string('route_leg', 5)->nullable();
            $table->string('dpt_airport_id', 5);
            $table->string('arr_airport_id', 5);
            $table->unsignedInteger('level')->nullable();
            $table->unsignedDecimal('distance')->nullable();
            $table->unsignedDecimal('planned_distance')->nullable();
            $table->unsignedInteger('flight_time')->nullable();
            $table->unsignedInteger('planned_flight_time')->nullable();
            $table->unsignedDecimal('zfw')->nullable();
            $table->unsignedDecimal('block_fuel')->nullable();
            $table->unsignedDecimal('fuel_used')->nullable();
            $table->decimal('landing_rate')->nullable();
            $table->text('route')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('source')->nullable()->default(0);
            $table->string('source_name', 25)->nullable();
            $table->tinyInteger('flight_type')->default(FlightType::PASSENGER);
            $table->tinyInteger('state')->default(PirepState::PENDING);
            $table->tinyInteger('status')->default(PirepStatus::SCHEDULED);
            $table->timestamps();

            $table->primary('id');
            $table->index('user_id');
            $table->index('flight_number');
            $table->index('dpt_airport_id');
            $table->index('arr_airport_id');
        });

        Schema::create('pirep_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pirep_id', \App\Interfaces\Model::ID_MAX_LENGTH);
            $table->unsignedInteger('user_id');
            $table->text('comment');
            $table->timestamps();
        });

        Schema::create('pirep_fares', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pirep_id', \App\Interfaces\Model::ID_MAX_LENGTH);
            $table->unsignedInteger('fare_id');
            $table->unsignedInteger('count')->nullable()->default(0);

            $table->index('pirep_id');
        });

        Schema::create('pirep_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('slug', 50)->nullable();
            $table->boolean('required')->nullable()->default(false);
        });

        Schema::create('pirep_field_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pirep_id', \App\Interfaces\Model::ID_MAX_LENGTH);
            $table->string('name', 50);
            $table->string('slug', 50)->nullable();
            $table->string('value')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();

            $table->index('pirep_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pireps');
        Schema::dropIfExists('pirep_comments');
        Schema::dropIfExists('pirep_fares');
        Schema::dropIfExists('pirep_fields');
        Schema::dropIfExists('pirep_field_values');
    }
}
