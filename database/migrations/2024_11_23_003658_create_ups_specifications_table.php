<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpsSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ups_specifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('app_user_id')->nullable();
            $table->string('unique_id')->nullable();
            $table->string('continuous_power')->nullable();
            $table->string('energy')->nullable();
            $table->string('dimensions')->nullable();
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
        Schema::dropIfExists('ups_specifications');
    }
}
