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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_id')->constrained('entreprises')->onDelete('cascade');
            $table->string('nom_client');
            $table->string('phone_client');
            $table->text('adresse_livraison');
            $table->integer('montant_total');
            $table->enum('statut', ['en_attente', 'valide', 'livre', 'annule'])->default('en_attente');
            $table->timestamps();
        });

                // Détails de la commande (Table pivot)
        Schema::create('commande_produit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->onDelete('cascade');
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->integer('quantite');
            $table->integer('prix_unitaire'); // Sécurité si le prix du produit change plus tard
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
        Schema::dropIfExists('commande_produit');
    }
};
