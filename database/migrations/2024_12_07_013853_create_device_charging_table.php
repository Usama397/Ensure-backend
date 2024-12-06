<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceChargingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_charging', function (Blueprint $table) {
            $table->id();
            $table->string('serial_key');
            $table->timestamp('charging_start_time');
            $table->timestamp('charging_end_time');
            $table->string('charging_status');
            $table->text('event');
            $table->date('specific_day');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_charging');
    }
}
