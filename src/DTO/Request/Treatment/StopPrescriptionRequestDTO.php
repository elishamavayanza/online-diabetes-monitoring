<?php

namespace App\DTO\Request\Treatment;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(title: 'StopPrescriptionRequestDTO')]
class StopPrescriptionRequestDTO
{
    public function __construct(
        #[Assert\Length(max: 5000)]
        #[OA\Property(type: 'string', nullable: true, example: 'Effets secondaires ressentis.')]
        public readonly ?string $reason = null,
    ) {}
}
