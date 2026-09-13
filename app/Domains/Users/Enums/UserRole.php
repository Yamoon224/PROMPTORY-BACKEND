<?php

namespace App\Domains\Users\Enums;

/**
 * Role grossier porte par le compte lui-meme.
 *
 * Distinct des permissions Spatie (`role`/`permission` middlewares) : celles-ci
 * gouvernent l'acces fin a chaque action, tandis que ce role ne fait que
 * distinguer le back-office de moderation du reste de la plateforme. Un compte
 * `admin` recoit systematiquement le role Spatie `admin` a la creation (voir
 * UserService), qui porte les vraies permissions.
 */
enum UserRole: string
{
    case Admin = 'admin';
    case User = 'user';
}
