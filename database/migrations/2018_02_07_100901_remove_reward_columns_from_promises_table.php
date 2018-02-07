<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveRewardColumnsFromPromisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promises', function (Blueprint $table) {
            $table->dropColumn(['reward_type', 'reward_name', 'reward_image_link']);
        });

        Schema::table('promises', function (Blueprint $table) {
            $table->renameColumn('reward_credits', 'credits');
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
            $table->text('reward_type')->default('');
            $table->text('reward_name')->nullable();
            $table->text('reward_image_link')->nullable();
        });

        Schema::table('promises', function (Blueprint $table) {
            $table->renameColumn('credits', 'reward_credits');
        });
    }
}
