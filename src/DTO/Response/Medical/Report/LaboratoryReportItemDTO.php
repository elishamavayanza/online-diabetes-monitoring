<?php

namespace App\DTO\Response\Medical\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Résultat de laboratoire dans le rapport de suivi')]
class LaboratoryReportItemDTO
{
    public function __construct(
        #[OA\Property(example: 'Bilan lipidique')]
        public readonly string $testName,
        #[OA\Property(example: 'Labo Central', nullable: true)]
        public readonly ?string $labName,
        #[OA\Property(example: '2026-08-15')]
        public readonly string $measuredAt,
        #[OA\Property(example: true)]
        public readonly bool $hasFile,
    ) {}
}
