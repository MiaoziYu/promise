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
            $table->integer('credits_earned')->default(0);
            $table->integer('credits_contributed')->default(0);
            $table->integer('max_streak')->default(0);
            $table->integer('max_streak_name')->default(0);
            $table->integer('promises_finished')->default(0);
            $table->integer('weekly_challenges_finished')->default(0);
            $table->integer('weekly_challenges_failed')->default(0);
            $table->integer('wish_tickets_amount')->default(0);
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
