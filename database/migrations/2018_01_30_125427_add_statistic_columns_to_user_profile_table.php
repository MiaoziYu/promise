<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatisticColumnsToUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->integer('credits_earned')->nullable();
            $table->integer('credits_contributed')->nullable();
            $table->integer('max_streak')->nullable();
            $table->integer('max_streak_name')->nullable();
            $table->integer('promises_finished')->nullable();
            $table->integer('weekly_challenges_finished')->nullable();
            $table->integer('weekly_challenges_failed')->nullable();
            $table->integer('wish_tickets_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['credits_earned', 'credits_contributed', 'max_streak', 'max_streak_name', 'promises_finished', 'weekly_challenges_finished', 'weekly_challenges_failed', 'wish_tickets_amount']);
        });
    }
}
