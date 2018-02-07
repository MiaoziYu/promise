<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyWishTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wish_tickets', function (Blueprint $table) {
            $table->integer('wish_id')->default(0);
        });

        Schema::table('wish_tickets', function (Blueprint $table) {
            $table->dropColumn(['name', 'image_link']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wish_tickets', function (Blueprint $table) {
            $table->dropColumn(['wish_id']);
        });

        Schema::table('wish_tickets', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('image_link')->nullable();
        });
    }
}
