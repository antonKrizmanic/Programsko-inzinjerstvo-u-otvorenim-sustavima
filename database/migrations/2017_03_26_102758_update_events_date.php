<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEventsDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dateTime('date_and_time_start')->nullable()->default(\Carbon\Carbon::now()->addDays(15));
            $table->dateTime('date_and_time_end')->nullable()->default(\Carbon\Carbon::now()->addDays(16));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('date_and_time_start');
            $table->dropColumn('date_and_time_end');
        });
    }
}
