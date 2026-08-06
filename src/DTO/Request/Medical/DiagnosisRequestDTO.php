<?php

namespace App\DTO\Request\Medical;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

class DiagnosisRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $patientId,

        #[Assert\NotBlank]
        public readonly string $doctorId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        public readonly string $conditionName,

        #[Assert\Length(max: 5000)]
        public readonly ?string $description,

        #[Assert\NotBlank]
        public readonly \DateTimeImmutable $diagnosedAt,

        #[Assert\NotBlank]
        #[Assert\Length(max: 50)]
        public readonly string $status,

        public readonly ?string $medicalRecordId
    ) {}
}
