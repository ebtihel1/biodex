<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['waste_category_id']);
            $table->dropForeign(['recycling_process_id']);
            $table->integer('waste_category_id')->change();
            $table->integer('recycling_process_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('waste_category_id')->constrained()->onDelete('cascade')->change();
            $table->foreignId('recycling_process_id')->constrained()->onDelete('cascade')->change();
        });
    }
};