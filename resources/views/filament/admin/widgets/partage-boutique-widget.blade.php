<x-filament-widgets::widget>
    <x-filament::section>
        
        @if($has_shop)
            <div class="relative flex flex-col lg:flex-row items-center justify-between gap-6 py-2 px-1 text-slate-900 dark:text-white">
                <div class="absolute right-0 top-0 -z-10 h-32 w-32 rounded-full bg-teal-500/5 blur-2xl"></div>

                <!-- Section Gauche -->
                <div class="flex flex-col sm:flex-row items-center gap-4 flex-grow w-full lg:w-auto">
                    <div class="text-center sm:text-left min-w-[160px]">
                        <h2 class="text-sm font-black tracking-tight flex items-center gap-1.5 justify-center sm:justify-start">
                            🚀 <span class="text-teal-600 dark:text-teal-400">{{ $raison_sociale }}</span>
                        </h2>
                    </div>

                    <!-- GESTION DE LA COPIE AVEC ALPINE.JS ICI -->
                    <div 
                        x-data="{ 
                            copie: false, 
                            url: '{{ $shop_url }}',
                            copier() {
                                if (navigator.clipboard && window.isSecureContext) {
                                    navigator.clipboard.writeText(this.url).then(() => this.indiquerCopie());
                                } else {
                                    const input = document.createElement('input'); input.value = this.url; document.body.appendChild(input); input.select(); document.execCommand('copy'); document.body.removeChild(input);
                                    this.indiquerCopie();
                                }
                            },
                            indiquerCopie() {
                                this.copie = true;
                                setTimeout(() => this.copie = false, 2000);
                            }
                        }"
                        class="flex items-center bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-lg p-0.5 w-full max-w-xs"
                    >
                        <span class="px-2 text-[11px] font-semibold text-teal-600 dark:text-teal-400 font-mono truncate select-all flex-grow">
                            {{ $shop_url }}
                        </span>
                        <button 
                            @click="copier()"
                            :class="copie ? 'bg-emerald-600' : 'bg-teal-600'"
                            class="px-3 py-1 rounded-md text-white font-bold text-[10px] shadow-sm hover:bg-teal-700 transition active:scale-95 flex items-center gap-1 shrink-0"
                        >
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c1.005.15 1.78 1.005 1.78 2.037v9.656c0 1.121-.821 2.07-1.93 2.201a41.365 41.365 0 0 1-7.14 0c-1.11-.13-1.93-1.08-1.93-2.201V5.925c0-1.032.774-1.888 1.78-2.037" />
                            </svg>
                            <span x-text="copie ? 'Copié !' : 'Copier'"></span>
                        </button>
                    </div>
                </div>

                <!-- Section Droite -->
                <div class="flex flex-col sm:flex-row items-center gap-3 justify-end w-full lg:w-auto shrink-0">
                    <div class="flex items-center gap-1.5 w-full sm:w-auto">
                        <a href="{{ $shop_url }}" target="_blank" class="flex-1 sm:flex-none flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg bg-slate-900 text-white dark:bg-white/10 font-bold text-[10px] shadow-sm transition active:scale-95">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                            Ouvrir
                        </a>
                        <a href="{{ $whatsapp_share_url }}" target="_blank" class="flex-1 sm:flex-none flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-500 text-white font-bold text-[10px] shadow-sm transition active:scale-95">
                            <svg class="h-3 w-3 fill-white" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.03 11.966.03c3.184.001 6.177 1.24 8.43 3.496 2.253 2.257 3.491 5.253 3.493 8.44-.004 6.62-5.34 11.938-11.91 11.938-2.007-.002-3.98-.51-5.731-1.474L0 24zm6.59-4.846c1.6.95 3.18 1.449 4.725 1.451 5.435 0 9.85-4.37 9.854-9.742.002-2.602-1.01-5.05-2.85-6.895-1.84-1.847-4.288-2.864-6.892-2.865-5.437 0-9.853 4.37-9.858 9.743-.002 1.97.514 3.894 1.496 5.594L1.5 21.077l4.831-1.265zM17.1 14.18c-.282-.141-1.67-.822-1.928-.916-.258-.094-.446-.14-.633.141-.188.281-.727.915-.892 1.102-.164.187-.329.21-.611.07-.281-.141-1.19-.439-2.268-1.4a8.293 8.293 0 0 1-1.569-1.954c-.164-.282-.018-.434.123-.574.127-.127.282-.329.423-.493.141-.164.188-.282.281-.47.094-.187.047-.351-.023-.492-.07-.14-.633-1.523-.867-2.063-.228-.547-.46-.473-.633-.482-.164-.005-.353-.006-.54-.006-.188 0-.493.07-.75.375-.258.282-.985.962-.985 2.345 0 1.383 1.008 2.72 1.149 2.907.14.187 1.984 3.03 4.807 4.246.671.29 1.196.463 1.605.593.675.214 1.288.184 1.774.11.542-.082 1.67-.68 1.905-1.337.235-.656.235-1.22.165-1.336-.07-.116-.258-.21-.516-.33z"/></svg>
                            Partager
                        </a>
                    </div>

                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800/80 p-1.5 rounded-lg border border-slate-100 dark:border-slate-700">
                        <img src="{{ $qr_code_url }}" class="h-8 w-8 object-contain rounded bg-white p-0.5" />
                        <a href="{{ $qr_code_url }}" download target="_blank" class="p-1.5 text-slate-500 hover:text-teal-600 transition">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
