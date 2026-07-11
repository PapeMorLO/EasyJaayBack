<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- ZONE CATALOGUE (7 colonnes) -->
        <div class="lg:col-span-7 space-y-4">
            <!-- Recherche / Scanner -->
            <div class="bg-white dark:bg-gray-900 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-white/10">
                <input 
                    type="text" 
                    wire:model.live="search" 
                    placeholder="Rechercher un produit (Nom ou Référence)..." 
                    class="w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm focus:ring-primary-500 focus:border-primary-500"
                />
            </div>

            <!-- Grille des Produits -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 overflow-y-auto max-h-[70vh] pr-2">
                @forelse($this->produits as $produit)
                    <button 
                        wire:click="ajouterAuPanier({{ $produit->id }})"
                        class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-white/10 shadow-sm text-left hover:border-primary-500 transition relative flex flex-col justify-between h-36 active:scale-95"
                    >
                        <!-- Badge Stock -->
                        <span class="absolute top-2 right-2 text-[10px] font-extrabold px-2 py-0.5 rounded-full {{ $produit->quantite_stock > 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700 animate-pulse' }}">
                            Stock: {{ $produit->quantite_stock }}
                        </span>

                        <div class="pt-2">
                            <h4 class="font-bold text-sm text-gray-900 dark:text-white line-clamp-2 leading-tight">
                                {{ $produit->designation }}
                            </h4>
                            <span class="text-xs text-gray-400 font-mono block mt-1">
                                {{ $produit->reference ?? 'Pas de réf.' }}
                            </span>
                        </div>

                        <div class="mt-2 pt-2 border-t border-gray-100 dark:border-white/5 w-full flex justify-between items-center">
                            <span class="font-black text-sm text-primary-600 dark:text-primary-400">
                                {{ number_format($produit->prix_vente, 0, ',', ' ') }} F
                            </span>
                            <span class="text-gray-400 bg-gray-100 dark:bg-gray-800 rounded p-1 text-xs">➕</span>
                        </div>
                    </button>
                @empty
                    <div class="col-span-full text-center py-10 text-gray-500">
                        Aucun produit trouvé.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ZONE CAISSE & PANIER (5 colonnes) -->
        <div class="lg:col-span-5 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 flex flex-col h-[80vh]">
            
            <!-- Liste du Panier -->
            @if($lastCommandeId)
                <!-- ÉCRAN DE SUCCÈS & IMPRESSION -->
                <div class="flex-1 flex flex-col items-center justify-center p-8 space-y-6 text-center">
                    <div class="h-20 w-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-4xl">
                        ✅
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white">Encaissement Réussi !</h3>
                    <p class="text-gray-500">La vente a été enregistrée et les stocks mis à jour.</p>
                    
                    <div class="w-full space-y-3 pt-4">
                        <!-- Ouvre le reçu dans un pop-up d'impression -->
                        <button 
                            onclick="window.open('{{ route('pos.recu', $lastCommandeId) }}', 'Recu', 'width=400,height=600')"
                            class="w-full py-4 bg-gray-900 dark:bg-white dark:text-black hover:bg-gray-800 text-white font-bold rounded-xl flex items-center justify-center gap-2"
                        >
                            🖨️ IMPRIMER LE TICKET
                        </button>

                        <button 
                            wire:click="nouvelleVente"
                            class="w-full py-4 bg-primary-100 text-primary-700 hover:bg-primary-200 font-bold rounded-xl"
                        >
                            Nouvelle Vente
                        </button>
                    </div>
                </div>
            @else
                <div class="p-6 overflow-y-auto flex-1 border-b border-gray-100 dark:border-white/5">
                    <h3 class="font-black text-lg mb-4 text-gray-900 dark:text-white flex items-center gap-2">
                        🛒 Panier
                    </h3>

                    @if(empty($panier))
                        <div class="text-center py-10 text-gray-400 text-sm font-medium">
                            Le panier est vide. Cliquez sur un produit pour commencer.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($panier as $item)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-white/5">
                                    <div class="flex-1 min-w-0 pr-2">
                                        <h5 class="font-bold text-xs text-gray-900 dark:text-white truncate">{{ $item['designation'] }}</h5>
                                        <span class="text-xs text-primary-600 font-bold">{{ number_format($item['prix'], 0, ',', ' ') }} F</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button wire:click="decrementerQuantite({{ $item['id'] }})" class="h-7 w-7 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded flex items-center justify-center font-bold text-gray-700 dark:text-gray-300">-</button>
                                        <span class="text-xs font-bold w-4 text-center dark:text-white">{{ $item['quantite'] }}</span>
                                        <button wire:click="incrementerQuantite({{ $item['id'] }})" class="h-7 w-7 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded flex items-center justify-center font-bold text-gray-700 dark:text-gray-300">+</button>
                                        <button wire:click="retirerDuPanier({{ $item['id'] }})" class="text-red-500 hover:text-red-700 ml-2">🗑️</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
           

                <!-- Fiche Client & Encaissement -->
                <div class="p-6 space-y-4 bg-gray-50 dark:bg-gray-900/50 rounded-b-xl">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 block mb-1">Client</label>
                            <input type="text" wire:model="nomClient" class="w-full text-sm p-2.5 border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 block mb-1">Téléphone</label>
                            <input type="tel" wire:model="phoneClient" placeholder="Optionnel" class="w-full text-sm p-2.5 border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-primary-500" />
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="flex justify-between items-center p-4 bg-gray-900 dark:bg-black text-white rounded-xl shadow-inner mt-4">
                        <span class="text-sm font-bold text-gray-400">TOTAL</span>
                        <span class="text-2xl font-black text-primary-400 font-mono">
                            {{ number_format($this->total, 0, ',', ' ') }} FCFA
                        </span>
                    </div>

                    <button 
                        wire:click="encaisser" 
                        @if(empty($panier)) disabled @endif
                        class="w-full py-4 bg-primary-600 hover:bg-primary-500 disabled:bg-gray-300 disabled:dark:bg-gray-700 text-white font-black rounded-xl transition active:scale-95 text-sm uppercase tracking-wide"
                    >
                        Encaisser la vente
                    </button>
                </div>
            @endif 
        </div>
    </div>
</x-filament-panels::page>