<?php

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
            $table->string('id', 12);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('airline_id');
            $table->unsignedInteger('aircraft_id')->nullable();
            $table->string('flight_id', 12)->nullable();
            $table->string('flight_number', 10)->nullable();
            $table->string('route_code', 5)->nullable();
            $table->string('route_leg', 5)->nullable();
            $table->string('dpt_airport_id', 5);
            $table->string('arr_airport_id', 5);
            $table->unsignedInteger('altitude')->nullable();
            $table->unsignedDecimal('flight_time', 19)->nullable();
            $table->unsignedDecimal('planned_flight_time', 19)->nullable();
            $table->unsignedDecimal('gross_weight', 19)->nullable();
            $table->unsignedDecimal('fuel_used', 19)->nullable();
            $table->text('route')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('source')->default(0);
            $table->tinyInteger('state')->default(PirepState::PENDING);
            $table->tinyInteger('status')->default(PirepStatus::SCHEDULED);
            $table->longText('raw_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
            $table->index('user_id');
            $table->index('flight_id');
            $table->index('dpt_airport_id');
            $table->index('arr_airport_id');
        });

        Schema::create('pirep_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pirep_id', 12);
            $table->unsignedInteger('user_id');
            $table->text('comment');
            $table->timestamps();
        });

        /*
         * Financial tables/fields
         */
        Schema::create('pirep_expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pirep_id', 12);
            $table->string('name');
            $table->double('value', 19, 2)->nullable();

            $table->index('pirep_id');
        });

        Schema::create('pirep_fares', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pirep_id', 12);
            $table->unsignedBigInteger('fare_id');
            $table->unsignedInteger('count')->nullable();

            $table->index('pirep_id');
        });

        /*
         * Additional PIREP data
         */
        Schema::create('pirep_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->boolean('required')->default(false);
        });

        Schema::create('pirep_field_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pirep_id', 12);
            $table->string('name', 50);
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
        Schema::dropIfExists('pirep_expenses');
        Schema::dropIfExists('pirep_fares');
        Schema::dropIfExists('pirep_fields');
        Schema::dropIfExists('pirep_field_values');
    }
}
