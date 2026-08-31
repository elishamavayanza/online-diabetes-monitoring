<?php

namespace App\DTO\Response\Medical\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Section résultats de laboratoire du rapport de suivi')]
class LaboratoryReportSectionDTO
{
    /**
     * @param LaboratoryReportItemDTO[] $results
     */
    public function __construct(
        #[OA\Property(example: 3)]
        public readonly int $count,
        public readonly array $results,
    ) {}
}
