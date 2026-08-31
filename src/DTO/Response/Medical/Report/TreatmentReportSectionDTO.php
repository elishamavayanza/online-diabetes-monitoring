<?php

namespace App\DTO\Response\Medical\Report;

use App\DTO\Response\Healthcare\Report\DistributionItemDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Section traitement / observance du rapport de suivi')]
class TreatmentReportSectionDTO
{
    /**
     * @param DistributionItemDTO[] $intakesByStatus
     */
    public function __construct(
        #[OA\Property(example: 2)]
        public readonly int $activePrescriptions,
        #[OA\Property(example: 85.5, nullable: true)]
        public readonly ?float $adherenceRate,
        #[OA\Property(example: 60)]
        public readonly int $totalIntakes,
        public readonly array $intakesByStatus,
    ) {}
}
