<?php

namespace App\Entity\Security;

/**
 * Nature de la suspension.
 */
enum SuspensionScope: string
{
    case ORGANIZATION = 'ORGANIZATION';
    case PROFESSIONAL = 'PROFESSIONAL';
    case PATIENT = 'PATIENT';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case) => $case->value, self::cases());
    }
}