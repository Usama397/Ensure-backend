<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('ups_raw', function (Blueprint $table) {
            $table->id();
            $table->text('raw_data'); // Store the full raw string
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ups_raw');
    }
};
