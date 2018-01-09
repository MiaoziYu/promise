<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDueDataToPromisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promises', function (Blueprint $table) {
            $table->dateTime('due_date')->nullable();
            $table->string('expired')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promises', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'expired']);
        });
    }
}
