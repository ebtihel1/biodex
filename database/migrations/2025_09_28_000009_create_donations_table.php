<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('donations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('waste_id')->constrained()->onDelete('cascade');
        $table->string('item_name');
        $table->string('condition');
        $table->string('status')->default('available');
        $table->text('description')->nullable();
        $table->json('images')->nullable();
        $table->boolean('pickup_required')->default(false);
        $table->text('pickup_address')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
