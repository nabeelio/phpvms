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
            $table->string('group')->nullable();
            $table->string('type')->nullable();
            $table->string('options')->nullable();
            $table->string('description')->nullable();

            $table->primary('id');
            $table->timestamps();
        });

        /**
         * Initial default settings
         */
        $settings = [
            [
                'id' => 'general_start_date',
                'order' => 1,
                'name' => 'Start Date',
                'group' => 'general',
                'value' => '',
                'type' => 'date',
                'description' => 'The date your VA started',
            ],
            [
                'id' => 'general_admin_email',
                'order' => 2,
                'name' => 'Admin Email',
                'group' => 'general',
                'value' => '',
                'type' => 'text',
                'description' => 'Email where notices, etc are sent',
            ],
            [
                'id' => 'general_currency',
                'order' => 3,
                'name' => 'Currency to Use',
                'group' => 'general',
                'value' => 'dollar',
                'type' => 'select',
                'options' => 'dollar,euro,gbp,yen,jpy,rupee,ruble',
                'description' => 'Currency to show in the interface',
            ],
            [
                'id' => 'flight_only_flights_from_current',
                'order' => 10,
                'name' => 'Flights from Current',
                'group' => 'flights',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Only allow flights from current location',
            ],
            [
                'id' => 'bids_disable_flight_on_bid',
                'order' => 20,
                'name' => 'Disable flight on bid',
                'group' => 'bids',
                'value' => true,
                'type' => 'boolean',
                'description' => 'When a flight is bid on, no one else can bid on it',
            ],
            [
                'id' => 'bids_allow_multiple_bids',
                'order' => 21,
                'name' => 'Allow multiple bids',
                'group' => 'bids',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Whether or not someone can bid on multiple flights',
            ],
            [
                'id' => 'pilots_id_length',
                'order' => 30,
                'name' => 'Pilot ID Length',
                'group' => 'pilots',
                'value' => 4,
                'type' => 'int',
                'description' => 'The length of a pilot\'s ID',
            ],
            [
                'id' => 'pilot_auto_accept',
                'order' => 31,
                'name' => 'Auto Accept New Pilot',
                'group' => 'pilots',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Automatically accept a pilot when they register',
            ],
            [
                'id' => 'pilot_auto_leave_days',
                'order' => 31,
                'name' => 'Pilot to ON LEAVE days',
                'group' => 'pilots',
                'value' => 30,
                'type' => 'int',
                'description' => 'Automatically set a pilot to ON LEAVE status after N days of no activity',
            ],
            [
                'id' => 'pilots_hide_inactive',
                'order' => 32,
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
