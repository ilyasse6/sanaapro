<?php

namespace App\Support;

/**
 * Identifiant d'entreprise pour la requête HTTP courante (employés / directeur).
 * Null = pas de filtre (ex. Super Admin).
 */
final class CurrentTenant
{
    private static ?int $id = null;

    public static function set(?int $id): void
    {
        self::$id = $id;
    }

    public static function id(): ?int
    {
        return self::$id;
    }

    public static function forget(): void
    {
        self::$id = null;
    }
}
