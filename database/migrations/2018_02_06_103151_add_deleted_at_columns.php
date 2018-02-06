<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedAtColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('habits', function ($table) {
            $table->softDeletes();
        });

        Schema::table('checklists', function ($table) {
            $table->softDeletes();
        });

        Schema::table('promises', function ($table) {
            $table->softDeletes();
        });

        Schema::table('weekly_challenges', function ($table) {
            $table->softDeletes();
        });

        Schema::table('wishes', function ($table) {
            $table->softDeletes();
        });

        Schema::table('wish_tickets', function ($table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('habits', function ($table) {
            $table->dropSoftDeletes();
        });

        Schema::table('checklists', function ($table) {
            $table->dropSoftDeletes();
        });

        Schema::table('promises', function ($table) {
            $table->dropSoftDeletes();
        });

        Schema::table('weekly_challenges', function ($table) {
            $table->dropSoftDeletes();
        });

        Schema::table('wishes', function ($table) {
            $table->dropSoftDeletes();
        });

        Schema::table('wish_tickets', function ($table) {
            $table->dropSoftDeletes();
        });
    }
}
