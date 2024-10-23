<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpsDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ups_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('unique_id')->nullable();
            $table->float('input_voltage', 10, 2)->nullable();
            $table->float('input_fault_voltage', 10, 2)->nullable();
            $table->float('output_voltage', 10, 2)->nullable();
            $table->float('output_current', 10, 2)->nullable();
            $table->float('output_frequency', 10, 2)->nullable();
            $table->float('battery_voltage', 10, 2)->nullable();
            $table->float('temperature', 10, 2)->nullable();
            $table->boolean('utility_fail')->nullable();
            $table->boolean('battery_low')->nullable();
            $table->boolean('avr_normal')->nullable();
            $table->boolean('ups_failed')->nullable();
            $table->boolean('ups_line_interactive')->nullable();
            $table->boolean('test_in_progress')->nullable();
            $table->boolean('shutdown_active')->nullable();
            $table->boolean('beeper_on')->nullable();
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
        Schema::dropIfExists('ups_data');
    }
}
