<?php

use App\Models\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stats', function (Blueprint $table) {
            $table->string('id');
            $table->string('value');
            $table->unsignedInteger('order');
            $table->string('type')->nullable();
            $table->string('description')->nullable();

            $table->primary('id');
            $table->timestamps();
        });

        $this->addCounterGroups([
            'flights' => 1,
        ]);

        /**
         * Initial default settings
         */
        $stats = [
            [
                'id' => $this->formatSettingId('flights.total_flights'),
                'order' => $this->getNextOrderNumber('flights'),
                'value' => 0,
                'type' => 'int',
                'description' => 'Total number of flights in the VA',
            ],
            [
                'id' => $this->formatSettingId('flights.total_time'),
                'order' => $this->getNextOrderNumber('flights'),
                'value' => 0,
                'type' => 'int',
                'description' => 'Total number of hours in the VA',
            ],
        ];

        $this->addData('stats', $stats);
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
