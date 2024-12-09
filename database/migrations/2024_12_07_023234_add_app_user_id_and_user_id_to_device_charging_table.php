<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppUserIdAndUserIdToDeviceChargingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_charging', function (Blueprint $table) {
            $table->unsignedBigInteger('app_user_id')->nullable()->after('id'); // Add app_user_id
            $table->unsignedBigInteger('user_id')->nullable()->after('app_user_id'); // Add user_id

            // Optional: Add foreign key constraints if applicable
            // $table->foreign('app_user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_charging', function (Blueprint $table) {
            $table->dropColumn(['app_user_id', 'user_id']);
        });
    }
}

