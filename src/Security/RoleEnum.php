<?php

namespace AcMarche\MeriteSportif\Security;

enum RoleEnum: string
{
    case ROLE_MERITE_ADMIN = 'ROLE_MERITE_ADMIN';
    case ROLE_MERITE = 'ROLE_MERITE';
    case ROLE_MERITE_CLUB = 'ROLE_MERITE_CLUB';

    public static function all(): array
    {
        return array_combine(
            array_map(fn($case) => $case->name, self::cases()),
            array_map(fn($case) => $case->name, self::cases())
        );
    }

}
