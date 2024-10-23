<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_logs', function (Blueprint $table) {
            $table->id();
            $table->string('uri')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('endpoint')->nullable();
            $table->string('method')->nullable();
            $table->string('ip')->nullable();
            $table->longText('request_body')->nullable();
            $table->longText('response')->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('action')->nullable();
            $table->string('controller')->nullable();
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
        Schema::dropIfExists('app_logs');
    }
}
