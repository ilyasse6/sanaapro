<?php

namespace App\Support;

/**
 * Montant en toutes lettres (français, orthographe traditionnelle) : « Soixante-neuf mille deux cent quarante-huit dirhams. »
 * Même algorithme que window.montantEnLettres dans user/facture/partials/form.blade.php.
 */
final class MontantEnLettres
{
    private const UNITES = [
        'zéro', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix',
        'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf',
    ];

    private const DIZAINES = [
        2 => 'vingt', 3 => 'trente', 4 => 'quarante', 5 => 'cinquante',
        6 => 'soixante', 7 => 'soixante', 8 => 'quatre-vingt', 9 => 'quatre-vingt',
    ];

    public static function dirhams(float $montant): string
    {
        $centimesTotal = (int) round(abs($montant) * 100);
        $entier = intdiv($centimesTotal, 100);
        $centimes = $centimesTotal % 100;

        $texte = self::nombre($entier).' '.($entier >= 1_000_000 && $entier % 1_000_000 === 0 ? 'de ' : '')
            .($entier > 1 ? 'dirhams' : 'dirham');
        if ($centimes > 0) {
            $texte .= ' et '.self::nombre($centimes).' '.($centimes > 1 ? 'centimes' : 'centime');
        }

        return mb_strtoupper(mb_substr($texte, 0, 1)).mb_substr($texte, 1).'.';
    }

    public static function nombre(int $n): string
    {
        if ($n === 0) {
            return 'zéro';
        }

        $parts = [];
        foreach ([[1_000_000_000, 'milliard'], [1_000_000, 'million']] as [$valeur, $nom]) {
            $q = intdiv($n, $valeur);
            if ($q > 0) {
                $parts[] = self::moinsDeMille($q, true).' '.$nom.($q > 1 ? 's' : '');
                $n %= $valeur;
            }
        }

        $milliers = intdiv($n, 1000);
        if ($milliers > 0) {
            $parts[] = $milliers === 1 ? 'mille' : self::moinsDeMille($milliers, false).' mille';
            $n %= 1000;
        }

        if ($n > 0) {
            $parts[] = self::moinsDeMille($n, true);
        }

        return implode(' ', $parts);
    }

    /** $pluriel : « cents » / « quatre-vingts » ne prennent pas de s devant « mille ». */
    private static function moinsDeMille(int $n, bool $pluriel): string
    {
        $centaines = intdiv($n, 100);
        $reste = $n % 100;

        $texte = '';
        if ($centaines > 0) {
            $texte = $centaines === 1 ? 'cent' : self::UNITES[$centaines].' cent';
            if ($reste === 0 && $centaines > 1 && $pluriel) {
                $texte .= 's';
            }
        }

        if ($reste > 0) {
            $texte .= ($texte !== '' ? ' ' : '').self::moinsDeCent($reste, $pluriel);
        }

        return $texte;
    }

    private static function moinsDeCent(int $n, bool $pluriel): string
    {
        if ($n < 20) {
            return self::UNITES[$n];
        }

        $d = intdiv($n, 10);
        $u = $n % 10;

        if ($d === 7 || $d === 9) {
            return self::DIZAINES[$d].($d === 7 && $u === 1 ? ' et ' : '-').self::UNITES[10 + $u];
        }

        if ($u === 0) {
            return self::DIZAINES[$d].($d === 8 && $pluriel ? 's' : '');
        }

        return self::DIZAINES[$d].($u === 1 && $d !== 8 ? ' et ' : '-').self::UNITES[$u];
    }
}
