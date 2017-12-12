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
            $table->increments('id');
            $table->unsignedInteger('order')->default(1);
            $table->string('name');
            $table->string('key');
            $table->string('value');
            $table->string('group')->nullable();
            $table->string('type')->nullable();
            $table->string('options')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->unique('key');
        });

        /**
         * Initial default settings
         */
        $settings = [
            [
                'order' => 1,
                'name' => 'Start Date',
                'group' => '',
                'key' => 'general.start_date',
                'value' => '',
                'type' => 'date',
                'description' => 'The date your VA started',
            ],
            [
                'order' => 2,
                'name' => 'Currency to Use',
                'group' => 'general',
                'key' => 'general.currency',
                'value' => 'dollar',
                'type' => 'text',
                'options' => 'dollar,euro,gbp,yen,jpy,rupee,ruble',
                'description' => 'Currency to show in the interface',
            ],
            [
                'order' => 10,
                'name' => 'Flights from Current',
                'group' => 'flights',
                'key' => 'flights.only_flights_from_current',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Only allow flights from current location',
            ],
            [
                'order' => 20,
                'name' => 'Disable flight on bid',
                'group' => 'bids',
                'key' => 'bids.disable_flight_on_bid',
                'value' => true,
                'type' => 'boolean',
                'description' => 'When a flight is bid on, should the flight be shown',
            ],
            [
                'order' => 21,
                'name' => 'Allow multiple bids',
                'group' => 'bids',
                'key' => 'bids.allow_multiple_bids',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Whether or not someone can bid on multiple flights',
            ],
            [
                'order' => 30,
                'name' => 'Hide Inactive Pilots',
                'group' => 'pilots',
                'key' => 'pilots.hide_inactive',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Don\'t show inactive pilots in the public view',
            ],
            [
                'order' => 31,
                'name' => 'Pilot ID Length',
                'group' => 'pilots',
                'key' => 'pilots.id_length',
                'value' => 4,
                'type' => 'int',
                'description' => 'The length of a pilot\'s ID',
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
