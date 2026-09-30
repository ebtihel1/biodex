<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            $table->unique(['campaign_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('participations', function (Blueprint $table) {
            $table->dropUnique(['campaign_id', 'user_id']);
        });
    }
};
