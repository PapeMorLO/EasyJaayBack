<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Informations que le vendeur remplit pour rassurer ses clients + son Pixel Facebook.
// Toutes les colonnes sont facultatives : les boutiques existantes ne changent pas.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            $table->text('a_propos')->nullable();               // 1-2 phrases de présentation
            $table->text('infos_livraison')->nullable();        // zones, prix, délais, retrait possible
            $table->json('paiements')->nullable();              // ["wave","orange_money","cash_livraison"...]
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('snapchat_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('pixel_facebook', 32)->nullable();   // identifiant du Pixel (chiffres)
        });
    }

    public function down(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            $table->dropColumn([
                'a_propos', 'infos_livraison', 'paiements',
                'facebook_url', 'instagram_url', 'snapchat_url', 'tiktok_url', 'pixel_facebook',
            ]);
        });
    }
};
