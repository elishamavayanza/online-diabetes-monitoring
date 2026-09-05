<?php

namespace App\DTO\Response\Identity;

/** Normalise les noms de fichiers historiques en URL publique d'avatar. */
final class AvatarUrl
{
    public static function toPublicUrl(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (preg_match('#^(?:https?:)?//#i', $value) || str_starts_with($value, 'data:')) {
            return $value;
        }

        if (str_starts_with($value, '/uploads/')) {
            return $value;
        }

        return '/uploads/files/avatars/' . ltrim($value, '/');
    }
}
