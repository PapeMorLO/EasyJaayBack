<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Votre boutique est prête à {{ $faites }}/{{ $total }}
        </x-slot>
        <x-slot name="description">
            Un inconnu qui arrive depuis une publicité vérifie ces points avant de commander.
        </x-slot>

        <div class="space-y-2">
            @foreach ($etapes as $etape)
                <div class="flex items-start gap-2 text-sm">
                    @if ($etape['ok'])
                        <x-filament::icon icon="heroicon-m-check-circle" class="h-5 w-5 shrink-0 text-success-600" />
                        <span class="text-gray-500 line-through">{{ $etape['titre'] }}</span>
                    @else
                        <x-filament::icon icon="heroicon-m-exclamation-circle" class="h-5 w-5 shrink-0 text-warning-600" />
                        <span>
                            <span class="font-semibold">{{ $etape['titre'] }}</span>
                            <span class="text-gray-500"> : {{ $etape['conseil'] }}</span>
                        </span>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            <x-filament::button tag="a" :href="$lienProfil" size="sm">
                Compléter « Ma boutique »
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
