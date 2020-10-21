<?php

use App\Contracts\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('enabled')->default(1);
            $table->timestamps();
        });

        $this->addModule(['name' => 'Awards']);
        $this->addModule(['name' => 'Sample']);
        $this->addModule(['name' => 'VMSAcars']);
        $this->addModule(['name' => 'Vacentral']);
        $this->addModule(['name' => 'TestModule']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules');
    }
}
