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
            $table->unsignedInteger('order')->default(99);
            $table->string('name');
            $table->string('value');
            $table->string('default')->nullable();
            $table->string('group')->nullable();
            $table->string('type')->nullable();
            $table->string('options')->nullable();
            $table->string('description')->nullable();

            $table->primary('id');
            $table->timestamps();
        });

        $this->addCounterGroups([
            'general' => 1,
            'flights' => 20,
            'bids' => 40,
            'pireps' => 60,
            'pilots' => 80,
        ]);

        /**
         * Initial default settings
         */
        $settings = [
            [
                'id' => $this->formatSettingId('general.start_date'),
                'order' => $this->getNextOrderNumber('general'),
                'name' => 'Start Date',
                'group' => 'general',
                'value' => '',
                'type' => 'date',
                'description' => 'The date your VA started',
            ],
            [
                'id' => $this->formatSettingId('general.admin_email'),
                'order' => $this->getNextOrderNumber('general'),
                'name' => 'Admin Email',
                'group' => 'general',
                'value' => '',
                'type' => 'text',
                'description' => 'Email where notices, etc are sent',
            ],
            [
                'id' => $this->formatSettingId('general.currency'),
                'order' => $this->getNextOrderNumber('general'),
                'name' => 'Currency to Use',
                'group' => 'general',
                'value' => 'dollar',
                'type' => 'select',
                'options' => 'dollar,euro,gbp,yen,jpy,rupee,ruble',
                'description' => 'Currency to show in the interface',
            ],
            [
                'id' => $this->formatSettingId('bids.disable_flight_on_bid'),
                'order' => $this->getNextOrderNumber('bids'),
                'name' => 'Disable flight on bid',
                'group' => 'bids',
                'value' => true,
                'type' => 'boolean',
                'description' => 'When a flight is bid on, no one else can bid on it',
            ],
            [
                'id' => $this->formatSettingId('bids.allow_multiple_bids'),
                'order' => $this->getNextOrderNumber('bids'),
                'name' => 'Allow multiple bids',
                'group' => 'bids',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Whether or not someone can bid on multiple flights',
            ],
            [
                'id' => $this->formatSettingId('pireps.duplicate_check_time'),
                'order' => $this->getNextOrderNumber('pireps'),
                'name' => 'PIREP duplicate time check',
                'group' => 'pireps',
                'value' => 10,
                'default' => 10,
                'type' => 'int',
                'description' => 'The time in minutes to check for a duplicate PIREP',
            ],
            [
                'id' => $this->formatSettingId('pireps.hide_cancelled_pireps'),
                'order' => $this->getNextOrderNumber('pireps'),
                'name' => 'Hide Cancelled PIREPs',
                'group' => 'pireps',
                'value' => true,
                'default' => true,
                'type' => 'boolean',
                'description' => 'Hide any cancelled PIREPs in the front-end',
            ],
            [
                'id' => $this->formatSettingId('pilots.id_length'),
                'order' => $this->getNextOrderNumber('pilots'),
                'name' => 'Pilot ID Length',
                'group' => 'pilots',
                'value' => 4,
                'default' => 4,
                'type' => 'int',
                'description' => 'The length of a pilot\'s ID',
            ],
            [
                'id' => $this->formatSettingId('pilot.auto_accept'),
                'order' => $this->getNextOrderNumber('pilots'),
                'name' => 'Auto Accept New Pilot',
                'group' => 'pilots',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Automatically accept a pilot when they register',
            ],
            [
                'id' => $this->formatSettingId('pilots.only_flights_from_current'),
                'order' => $this->getNextOrderNumber('pilots'),
                'name' => 'Flights from Current',
                'group' => 'pilots',
                'value' => false,
                'type' => 'boolean',
                'description' => 'Only show/allow flights from their current location',
            ],
            [
                'id' => $this->formatSettingId('pilot.auto_leave_days'),
                'order' => $this->getNextOrderNumber('pilots'),
                'name' => 'Pilot to ON LEAVE days',
                'group' => 'pilots',
                'value' => 30,
                'default' => 30,
                'type' => 'int',
                'description' => 'Automatically set a pilot to ON LEAVE status after N days of no activity',
            ],
            [
                'id' => $this->formatSettingId('pilots.hide_inactive'),
                'order' => $this->getNextOrderNumber('pilots'),
                'name' => 'Hide Inactive Pilots',
                'group' => 'pilots',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Don\'t show inactive pilots in the public view',
            ],
        ];

        $this->addData('settings', $settings);
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
