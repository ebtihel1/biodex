<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropWasteIdForeignKey extends Migration
{
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['waste_id']);
            $table->integer('waste_id')->change();
        });
    }

    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->foreign('waste_id')->references('id')->on('wastes')->onDelete('cascade');
        });
    }
}