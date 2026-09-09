<?php

namespace App\Service\Notification;

/**
 * Évalue un minuteur CRON (5 champs) à un instant donné.
 * Format : minute heure jour-mois mois jour-semaine
 * Gère les étoiles, pas de pas, listes et plages.
 */
class CronExpression
{
    /**
     * Vérifie si l'expression CRON correspond à l'instant $now.
     */
    public static function isDue(string $expression, \DateTimeImmutable $now): bool
    {
        $parts = preg_split('/\s+/', trim($expression));
        if (!$parts || count($parts) !== 5) {
            return false;
        }

        [$minute, $hour, $dayOfMonth, $month, $dayOfWeek] = $parts;

        return self::matchesField($minute, (int) $now->format('i'))
            && self::matchesField($hour, (int) $now->format('G'))
            && self::matchesField($dayOfMonth, (int) $now->format('j'))
            && self::matchesField($month, (int) $now->format('n'))
            && self::matchesWeekDay($dayOfWeek, (int) $now->format('N'));
    }

    /**
     * Vérifie que $value correspond à un champ (minute/heure/jour/mois).
     */
    private static function matchesField(string $field, int $value): bool
    {
        $field = str_replace('?', '*', $field);
        if ($field === '*') {
            return true;
        }

        foreach (explode(',', $field) as $part) {
            if (self::matchesFieldPart($part, $value)) {
                return true;
            }
        }

        return false;
    }

    private static function matchesFieldPart(string $part, int $value): bool
    {
        if (str_contains($part, '/')) {
            [$range, $step] = explode('/', $part, 2);
            $step = (int) $step;
            if ($step <= 0) {
                return false;
            }
            [$start, $end] = self::parseRange($range);
            if ($value < $start || $value > $end) {
                return false;
            }
            return (($value - $start) % $step) === 0;
        }

        [$start, $end] = self::parseRange($part);
        return $value >= $start && $value <= $end;
    }

    /**
     * Le jour de la semaine est au format CRON (0-7, 0 et 7 = dimanche).
     * $valueN suit la norme ISO (1 = lundi … 7 = dimanche).
     */
    private static function matchesWeekDay(string $field, int $valueN): bool
    {
        $field = str_replace('?', '*', $field);
        $cronValue = $valueN % 7; // 0 => dimanche, 1-6 => lundi-samedi

        if (self::matchesField($field, $cronValue)) {
            return true;
        }

        // 7 est un alias de 0 (dimanche).
        if ($valueN === 7 && in_array('7', array_map('trim', explode(',', $field)), true)) {
            return true;
        }

        return false;
    }

    /**
     * Parse "value", "start-end" ou "*" en une plage [min, max].
     *
     * @return array{0:int,1:int}
     */
    private static function parseRange(string $range): array
    {
        $range = trim($range);
        if ($range === '*') {
            return [0, 59];
        }

        if (str_contains($range, '-')) {
            [$start, $end] = explode('-', $range, 2);
            return [(int) $start, (int) $end];
        }

        $v = (int) $range;
        return [$v, $v];
    }
}