<?php

namespace App\Service\Common;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;

/**
 * Parse les paramètres de liste partagés (from / to / limit) depuis la query string.
 */
final class ListQueryParams
{
    public function __construct(
        public readonly ?DateTimeImmutable $from = null,
        public readonly ?DateTimeImmutable $to = null,
        public readonly ?int $limit = null,
    ) {
    }

    public static function fromRequest(Request $request, ?int $defaultLimit = null): self
    {
        $fromRaw = $request->query->get('from');
        $toRaw = $request->query->get('to');
        $limitRaw = $request->query->get('limit');

        $from = null;
        $to = null;
        if (is_string($fromRaw) && $fromRaw !== '') {
            try {
                $from = new DateTimeImmutable($fromRaw);
            } catch (\Exception) {
                $from = null;
            }
        }
        if (is_string($toRaw) && $toRaw !== '') {
            try {
                $to = new DateTimeImmutable($toRaw);
            } catch (\Exception) {
                $to = null;
            }
        }

        $limit = $defaultLimit;
        if ($limitRaw !== null && $limitRaw !== '') {
            $limit = max(1, (int) $limitRaw);
        }

        return new self($from, $to, $limit);
    }
}
