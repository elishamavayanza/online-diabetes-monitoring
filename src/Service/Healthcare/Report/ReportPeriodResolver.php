<?php

namespace App\Service\Healthcare\Report;

use DateTimeImmutable;

final class ReportPeriodResolver
{
  public static function resolve(?string $from, ?string $to, ?string $preset): array
  {
    $now = new DateTimeImmutable('today');
    $preset = $preset ?: ($from && $to ? 'custom' : 'month');

    return match ($preset) {
      'month' => self::monthPeriod($now),
      'quarter' => self::quarterPeriod($now),
      'year' => self::yearPeriod($now),
      default => self::customPeriod($from, $to, $now),
    };
  }

  /**
   * @return array{from: DateTimeImmutable, to: DateTimeImmutable, previousFrom: DateTimeImmutable, previousTo: DateTimeImmutable, preset: string}
   */
  private static function monthPeriod(DateTimeImmutable $now): array
  {
    $from = $now->modify('first day of this month')->setTime(0, 0);
    $to = $now->modify('last day of this month')->setTime(23, 59, 59);
    $previousFrom = $from->modify('-1 month');
    $previousTo = $to->modify('-1 month');

    return compact('from', 'to', 'previousFrom', 'previousTo') + ['preset' => 'month'];
  }

  /**
   * @return array{from: DateTimeImmutable, to: DateTimeImmutable, previousFrom: DateTimeImmutable, previousTo: DateTimeImmutable, preset: string}
   */
  private static function quarterPeriod(DateTimeImmutable $now): array
  {
    $month = (int) $now->format('n');
    $quarterStartMonth = (int) (floor(($month - 1) / 3) * 3 + 1);
    $from = $now->setDate((int) $now->format('Y'), $quarterStartMonth, 1)->setTime(0, 0);
    $to = $from->modify('+2 months')->modify('last day of this month')->setTime(23, 59, 59);
    $days = $from->diff($to)->days + 1;
    $previousTo = $from->modify('-1 day')->setTime(23, 59, 59);
    $previousFrom = $previousTo->modify(sprintf('-%d days', $days - 1))->setTime(0, 0);

    return compact('from', 'to', 'previousFrom', 'previousTo') + ['preset' => 'quarter'];
  }

  /**
   * @return array{from: DateTimeImmutable, to: DateTimeImmutable, previousFrom: DateTimeImmutable, previousTo: DateTimeImmutable, preset: string}
   */
  private static function yearPeriod(DateTimeImmutable $now): array
  {
    $from = $now->setDate((int) $now->format('Y'), 1, 1)->setTime(0, 0);
    $to = $now->setDate((int) $now->format('Y'), 12, 31)->setTime(23, 59, 59);
    $previousFrom = $from->modify('-1 year');
    $previousTo = $to->modify('-1 year');

    return compact('from', 'to', 'previousFrom', 'previousTo') + ['preset' => 'year'];
  }

  /**
   * @return array{from: DateTimeImmutable, to: DateTimeImmutable, previousFrom: DateTimeImmutable, previousTo: DateTimeImmutable, preset: string}
   */
  private static function customPeriod(?string $from, ?string $to, DateTimeImmutable $now): array
  {
    $fromDate = $from
      ? DateTimeImmutable::createFromFormat('Y-m-d', $from)?->setTime(0, 0)
      : $now->modify('-29 days')->setTime(0, 0);
    $toDate = $to
      ? DateTimeImmutable::createFromFormat('Y-m-d', $to)?->setTime(23, 59, 59)
      : $now->setTime(23, 59, 59);

    if (!$fromDate || !$toDate || $fromDate > $toDate) {
      $fromDate = $now->modify('-29 days')->setTime(0, 0);
      $toDate = $now->setTime(23, 59, 59);
    }

    $days = $fromDate->diff($toDate)->days + 1;
    $previousTo = $fromDate->modify('-1 day')->setTime(23, 59, 59);
    $previousFrom = $previousTo->modify(sprintf('-%d days', $days - 1))->setTime(0, 0);

    return [
      'from' => $fromDate,
      'to' => $toDate,
      'previousFrom' => $previousFrom,
      'previousTo' => $previousTo,
      'preset' => 'custom',
    ];
  }
}
