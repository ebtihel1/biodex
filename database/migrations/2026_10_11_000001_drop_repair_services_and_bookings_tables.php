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
        // Supprimer la table bookings (dépend de repair_services)
        Schema::dropIfExists('bookings');

        // Supprimer la table repair_services
        Schema::dropIfExists('repair_services');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer la table repair_services
        Schema::create('repair_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('duration')->nullable();
            $table->string('category');
            $table->boolean('is_available')->default(true);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Recréer la table bookings
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('repair_service_id')->constrained()->onDelete('cascade');
            $table->dateTime('booking_date');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->text('item_description')->nullable();
            $table->decimal('estimated_cost', 8, 2)->nullable();
            $table->timestamps();
        });
    }
};
