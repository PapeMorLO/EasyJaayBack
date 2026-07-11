<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu N° {{ $commande->id }}</title>
    <style>
        /* Styles de base pour l'aperçu écran */
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f0f0f0;
            color: #000;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .ticket {
            background: #fff;
            width: 80mm; /* Largeur standard imprimante thermique */
            margin: 0 auto;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-top { border-top: 1px dashed #000; margin-top: 5px; padding-top: 5px; }
        .border-bottom { border-bottom: 1px dashed #000; margin-bottom: 5px; padding-bottom: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { text-align: left; padding: 3px 0; }
        th:last-child, td:last-child { text-align: right; }
        
        /* Styles appliqués UNIQUEMENT lors de l'impression */
        @media print {
            body { background: #fff; padding: 0; margin: 0; }
            .ticket { width: 100%; box-shadow: none; padding: 0; }
            /* Cacher les boutons ou éléments inutiles à l'impression */
            .no-print { display: none !important; }
            @page { margin: 0; } /* Supprime les headers/footers auto du navigateur */
        }
    </style>
</head>
<body onload="window.print();">

    <div class="ticket">
        <div class="text-center">
            <h2 style="margin:0;">{{ $commande->entreprise->raison_sociale }}</h2>
            <p style="margin:5px 0;">
                Tél: {{ $commande->entreprise->contact_call }}<br>
                {{ $commande->entreprise->adresse ?? 'Dakar, Sénégal' }}
            </p>
        </div>

        <div class="border-top border-bottom">
            <p style="margin:2px 0;">Date: {{ $commande->created_at->format('d/m/Y H:i') }}</p>
            <p style="margin:2px 0;">Ticket N°: #{{ str_pad($commande->id, 5, '0', STR_PAD_LEFT) }}</p>
            <p style="margin:2px 0;">Client: {{ $commande->nom_client }}</p>
        </div>

        <table>
            <thead>
                <tr class="border-bottom">
                    <th>QTE x DESIGNATION</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commande->produits as $produit)
                <tr>
                    <td>{{ $produit->pivot->quantite }}x {{ Str::limit($produit->designation, 15) }}</td>
                    <td>{{ number_format($produit->pivot->prix_unitaire * $produit->pivot->quantite, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-top" style="font-size: 14px;">
            <table style="margin: 0;">
                <tr>
                    <td class="font-bold">TOTAL À PAYER:</td>
                    <td class="font-bold">{{ number_format($commande->montant_total, 0, ',', ' ') }} FCFA</td>
                </tr>
            </table>
        </div>

        <div class="text-center border-top" style="margin-top: 15px;">
            <p>Merci de votre visite !</p>
            <p style="font-size: 10px;">Propulsé par EasyJaay</p>
        </div>
        
        <div class="text-center no-print" style="margin-top: 20px;">
            <button onclick="window.close()" style="padding: 10px; background: #ddd; border: none; cursor:pointer;">Fermer</button>
        </div>
    </div>

</body>
</html>