<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Priorité 1 : on rend les commandes fiables et on commence à mesurer.
 *
 *  - commande_produit : on garde le NOM du produit, la taille et la couleur choisies,
 *    et supprimer un produit ne supprime plus l'historique des ventes.
 *  - commandes        : d'où vient la commande (source) et par quel canal (canal).
 *  - produits         : la référence est unique PAR boutique, et image1 peut contenir plus de 5 photos.
 *  - evenements       : un petit journal (visite, produit vu, ajout au panier) pour calculer l'entonnoir.
 *  - notifications    : nécessaire pour prévenir le vendeur d'une nouvelle commande dans l'admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Lignes de commande : nom du produit + options choisies par le client
        Schema::table('commande_produit', function (Blueprint $table) {
            $table->string('designation')->nullable()->after('produit_id');
            $table->string('taille')->nullable()->after('prix_unitaire');
            $table->string('couleur')->nullable()->after('taille');
        });

        // Anciennes lignes : on copie le nom actuel du produit
        foreach (DB::table('produits')->select('id', 'designation')->get() as $produit) {
            DB::table('commande_produit')
                ->where('produit_id', $produit->id)
                ->whereNull('designation')
                ->update(['designation' => $produit->designation]);
        }

        // Supprimer un produit ne doit plus supprimer ses lignes de commande
        Schema::table('commande_produit', function (Blueprint $table) {
            $table->dropForeign(['produit_id']);
        });
        Schema::table('commande_produit', function (Blueprint $table) {
            $table->unsignedBigInteger('produit_id')->nullable()->change();
        });
        Schema::table('commande_produit', function (Blueprint $table) {
            $table->foreign('produit_id')->references('id')->on('produits')->nullOnDelete();
        });

        // 2) Commandes : origine + canal
        Schema::table('commandes', function (Blueprint $table) {
            $table->string('source', 20)->default('web')->after('statut');  // web | direct | pos
            $table->string('canal', 30)->nullable()->after('source');       // facebook, snapchat, ...
            $table->index(['entreprise_id', 'statut', 'created_at']);
        });

        // Les ventes déjà faites à la caisse sont reconnues par leur adresse
        DB::table('commandes')
            ->where('adresse_livraison', 'Vente au comptoir (POS)')
            ->update(['source' => 'pos']);

        // 3) Produits : référence unique par boutique, et image1 sans limite de 255 caractères
        Schema::table('produits', function (Blueprint $table) {
            $table->dropUnique('produits_reference_unique');
        });
        Schema::table('produits', function (Blueprint $table) {
            $table->unique(['entreprise_id', 'reference']);
            $table->text('image1')->nullable()->change();
        });

        // 4) Journal des évènements pour l'entonnoir (visite -> produit -> panier -> commande)
        Schema::create('evenements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_id')->constrained('entreprises')->cascadeOnDelete();
            $table->unsignedBigInteger('produit_id')->nullable();
            $table->string('type', 30);
            $table->string('canal', 30)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['entreprise_id', 'type', 'created_at']);
        });

        // 5) Notifications Filament (cloche dans l'admin)
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('evenements');

        Schema::table('produits', function (Blueprint $table) {
            $table->dropUnique(['entreprise_id', 'reference']);
        });
        Schema::table('produits', function (Blueprint $table) {
            $table->unique('reference', 'produits_reference_unique');
        });

        Schema::table('commandes', function (Blueprint $table) {
            $table->dropIndex(['entreprise_id', 'statut', 'created_at']);
            $table->dropColumn(['source', 'canal']);
        });

        Schema::table('commande_produit', function (Blueprint $table) {
            $table->dropColumn(['designation', 'taille', 'couleur']);
        });
    }
};
