<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpsDataLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ups_data_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ups_data_id'); // Reference to UPS data
            $table->unsignedBigInteger('user_id');
            $table->json('data'); // Store the request data as JSON
            $table->string('action'); // 'created' or 'updated'
            $table->timestamps();
    
            // Foreign key constraint
            $table->foreign('ups_data_id')->references('id')->on('ups_data')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ups_data_logs');
    }
}
