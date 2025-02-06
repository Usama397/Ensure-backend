<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ups_data', function (Blueprint $table) {
            $table->boolean('charging_status')->after('beeper_on')->default(0);
        });
    }

    public function down()
    {
        Schema::table('ups_data', function (Blueprint $table) {
            $table->dropColumn('charging_status');
        });
    }
};
