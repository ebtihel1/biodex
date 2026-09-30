<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('collection_points', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('contact_phone')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('opening_hours')->nullable();
            $table->json('accepted_categories')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('collection_points');
    }
};