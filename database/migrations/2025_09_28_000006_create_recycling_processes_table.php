<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recycling_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_id')->constrained()->onDelete('cascade');
            $table->foreignId('responsible_user_id')->constrained('users')->onDelete('cascade');
            $table->string('process_type');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recycling_processes');
    }
};