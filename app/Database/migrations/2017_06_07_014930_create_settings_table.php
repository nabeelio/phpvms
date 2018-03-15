<?php

use App\Models\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('id');
            $table->unsignedInteger('offset')->default(0);
            $table->unsignedInteger('order')->default(99);
            $table->string('key');
            $table->string('name');
            $table->string('value');
            $table->string('default')->nullable();
            $table->string('group')->nullable();
            $table->string('type')->nullable();
            $table->text('options')->nullable();
            $table->string('description')->nullable();

            $table->primary('id');
            $table->index('key');
            $table->timestamps();
        });

        /**
         * Initial default settings
         */

        $this->addSetting('general.start_date', [
            'name' => 'Start Date',
            'group' => 'general',
            'value' => '',
            'type' => 'date',
            'description' => 'The date your VA started',
        ]);

        $this->addSetting('general.admin_email', [
            'name' => 'Admin Email',
            'group' => 'general',
            'value' => '',
            'type' => 'text',
            'description' => 'Email where notices, etc are sent',
        ]);

        /*$this->addSetting('general.currency', [
            'name' => 'Currency to Use',
            'group' => 'general',
            'value' => 'USD',
            'type' => 'select',
            'options' => 'USD,EUR,GBP,JPY,RUB',
            'description' => 'Currency to use. NOTE: If you change this, then current amounts won\'t be converted',
        ]);*/

        $this->addSetting('units.distance', [
            'name' => 'Distance Units',
            'group' => 'units',
            'value' => 'NM',
            'type' => 'select',
            'options' => 'km=kilometers,mi=miles,NM=nautical miles',
            'description' => 'The distance unit for display',
        ]);

        $this->addSetting('units.weight', [
            'name' => 'Weight Units',
            'group' => 'units',
            'value' => 'lbs',
            'type' => 'select',
            'options' => 'lbs,kg',
            'description' => 'The weight unit for display',
        ]);

        $this->addSetting('units.speed', [
            'name' => 'Speed Units',
            'group' => 'units',
            'value' => 'knot',
            'type' => 'select',
            'options' => 'km/h,knot',
            'description' => 'The speed unit for display',
        ]);

        $this->addSetting('units.altitude', [
            'name' => 'Altitude Units',
            'group' => 'units',
            'value' => 'ft',
            'type' => 'select',
            'options' => 'ft=feet,m=meters',
            'description' => 'The altitude unit for display',
        ]);

        $this->addSetting('units.fuel', [
            'name' => 'Fuel Units',
            'group' => 'units',
            'value' => 'lbs',
            'type' => 'select',
            'options' => 'lbs,kg',
            'description' => 'The units for fuel for display',
        ]);

        $this->addSetting('units.volume', [
            'name' => 'Volume Units',
            'group' => 'units',
            'value' => 'gallons',
            'type' => 'select',
            'options' => 'gallons,l=liters',
            'description' => 'The units for fuel for display',
        ]);

        /**
         * BIDS
         */

        $this->addSetting('bids.disable_flight_on_bid', [
            'name' => 'Disable flight on bid',
            'group' => 'bids',
            'value' => true,
            'type' => 'boolean',
            'description' => 'When a flight is bid on, no one else can bid on it',
        ]);

        $this->addSetting('bids.allow_multiple_bids', [
            'name' => 'Allow multiple bids',
            'group' => 'bids',
            'value' => true,
            'type' => 'boolean',
            'description' => 'Whether or not someone can bid on multiple flights',
        ]);

        $this->addSetting('bids.expire_time', [
            'name' => 'Expire Time',
            'group' => 'bids',
            'value' => 48,
            'type' => 'int',
            'description' => 'Number of hours to expire bids after',
        ]);

        /**
         * PIREPS
         */

        $this->addSetting('pireps.duplicate_check_time', [
            'name' => 'PIREP duplicate time check',
            'group' => 'pireps',
            'value' => 10,
            'type' => 'int',
            'description' => 'The time in minutes to check for a duplicate PIREP',
        ]);

        /*$this->addSetting('pireps.hide_cancelled_pireps', [
            'name' => 'Hide Cancelled PIREPs',
            'group' => 'pireps',
            'value' => true,
            'type' => 'boolean',
            'description' => 'Hide any cancelled PIREPs in the front-end',
        ]);*/

        $this->addSetting('pireps.restrict_aircraft_to_rank', [
            'name' => 'Restrict Aircraft to Ranks',
            'group' => 'pireps',
            'value' => true,
            'type' => 'boolean',
            'description' => 'Aircraft that can be flown are restricted to a user\'s rank',
        ]);

        $this->addSetting('pireps.only_aircraft_at_dep_airport', [
            'name' => 'Restrict Aircraft At Departure',
            'group' => 'pireps',
            'value' => false,
            'type' => 'boolean',
            'description' => 'Only allow aircraft that are at the departure airport',
        ]);

        $this->addSetting('pireps.remove_bid_on_accept', [
            'name' => 'Remove bid on accept',
            'group' => 'pireps',
            'value' => false,
            'type' => 'boolean',
            'description' => 'When a PIREP is accepted, remove the bid, if it exists',
        ]);

        /**
         * PILOTS
         */

        $this->addSetting('pilots.id_length', [
            'name' => 'Pilot ID Length',
            'group' => 'pilots',
            'value' => 4,
            'default' => 4,
            'type' => 'int',
            'description' => 'The length of a pilot\'s ID',
        ]);

        $this->addSetting('pilots.auto_accept', [
            'name' => 'Auto Accept New Pilot',
            'group' => 'pilots',
            'value' => true,
            'type' => 'boolean',
            'description' => 'Automatically accept a pilot when they register',
        ]);

        $this->addSetting('pilots.home_hubs_only', [
            'name' => 'Hubs as home airport',
            'group' => 'pilots',
            'value' => false,
            'type' => 'boolean',
            'description' => 'Pilots can only select hubs as their home airport',
        ]);

        $this->addSetting('pilots.only_flights_from_current', [
            'name' => 'Flights from Current',
            'group' => 'pilots',
            'value' => false,
            'type' => 'boolean',
            'description' => 'Only show/allow flights from their current location',
        ]);

        $this->addSetting('pilots.auto_leave_days', [
            'name' => 'Pilot to ON LEAVE days',
            'group' => 'pilots',
            'value' => 30,
            'default' => 30,
            'type' => 'int',
            'description' => 'Automatically set a pilot to ON LEAVE status after N days of no activity',
        ]);

        $this->addSetting('pilots.hide_inactive', [
            'name' => 'Hide Inactive Pilots',
            'group' => 'pilots',
            'value' => true,
            'type' => 'boolean',
            'description' => 'Don\'t show inactive pilots in the public view',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
