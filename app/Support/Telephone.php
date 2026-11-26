<?php

namespace App\Support;

/**
 * Numéros de téléphone sénégalais.
 *
 * WhatsApp (wa.me) veut le numéro complet sans "+" : 221771234567.
 * Les vendeurs et les clients tapent plutôt "77 123 45 67" : on convertit ici, à un seul endroit.
 */
class Telephone
{
    // Un numéro valide : 221 + 9 chiffres
    public const REGEX = '/^221\d{9}$/';

    public static function normaliser(?string $numero): ?string
    {
        if ($numero === null) {
            return null;
        }

        $chiffres = preg_replace('/\D/', '', $numero);   // on garde seulement les chiffres

        if (str_starts_with($chiffres, '00221')) {        // 00221 77... -> 221 77...
            $chiffres = substr($chiffres, 2);
        }

        if (strlen($chiffres) === 9) {                    // 77 123 45 67 -> 221 77 123 45 67
            $chiffres = '221' . $chiffres;
        }

        return $chiffres;
    }

    public static function estValide(?string $numero): bool
    {
        return $numero !== null && preg_match(self::REGEX, $numero) === 1;
    }
}
