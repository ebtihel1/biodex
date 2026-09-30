<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->date('deadline_registration')->nullable()->after('description');
            $table->string('city')->nullable()->after('deadline_registration');
            $table->string('region')->nullable()->after('city');
            $table->integer('participants_count')->default(0)->after('region');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['deadline_registration', 'city', 'region', 'participants_count']);
        });
    }
};
