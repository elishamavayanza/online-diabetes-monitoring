<?php

namespace App\DTO\Response\Medical\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Période analysée pour le rapport de suivi patient')]
class PatientFollowUpReportPeriodDTO
{
    public function __construct(
        #[OA\Property(example: '2026-08-01')]
        public readonly string $from,
        #[OA\Property(example: '2026-08-31')]
        public readonly string $to,
    ) {}
}
