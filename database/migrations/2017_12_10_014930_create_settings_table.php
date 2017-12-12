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
            $table->integer('order')->default(1);
            $table->string('name');
            $table->string('key');
            $table->string('value');
            $table->string('group')->nullable();
            $table->text('type')->nullable();
            $table->text('options')->nullable();
            $table->text('description')->nullable();
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
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Only allow flights from current location',
            ],
            [
                'order' => 20,
                'name' => 'Hide Inactive Pilots',
                'group' => 'pilots',
                'key' => 'pilots.hide_inactive',
                'value' => 'true',
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
