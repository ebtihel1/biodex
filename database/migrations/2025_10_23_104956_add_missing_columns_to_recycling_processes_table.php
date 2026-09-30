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
        Schema::table('recycling_processes', function (Blueprint $table) {
            // Renommer process_type en method
            $table->renameColumn('process_type', 'method');
            
            // Ajouter les colonnes manquantes
            $table->decimal('output_quantity', 10, 2)->nullable();
            $table->string('output_quality')->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recycling_processes', function (Blueprint $table) {
            // Renommer method en process_type
            $table->renameColumn('method', 'process_type');
            
            // Supprimer les colonnes ajoutées
            $table->dropColumn(['output_quantity', 'output_quality', 'notes']);
        });
    }
};
