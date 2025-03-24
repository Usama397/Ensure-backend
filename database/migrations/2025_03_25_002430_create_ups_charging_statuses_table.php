<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpsChargingStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ups_charging_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('serial_key')->unique();
            $table->dateTime('charging_start_time')->nullable();
            $table->dateTime('charging_end_time')->nullable();
            $table->string('charging_status');
            $table->string('event');
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
        Schema::dropIfExists('ups_charging_statuses');
    }
}
