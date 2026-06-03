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
        Schema::create('entreprises', function (Blueprint $table) {
            $table->id();
            $table->string('raison_sociale');
            $table->string('slug')->unique(); // ex: 'sokhna-cosmetiques'
            $table->string('logo')->nullable();
            $table->string('couleur_theme')->default('#10b981'); // Vert par défaut
            $table->string('adresse')->nullable();
            $table->string('contact_call');
            $table->string('contact_whatsapp'); // Numéro pour le tunnel de commande
            $table->string('site_web')->nullable();
            $table->boolean('is_active')->default(true); // Pour couper la boutique si impayé
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprises');
    }
};
