<?php

namespace App\DTO\Request\Medical;

use Symfony\Component\Validator\Constraints as Assert;

class LaboratoryResultRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        public readonly string $testName,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        public readonly ?string $fileUrl,

        #[Assert\Length(max: 150)]
        public readonly ?string $labName
    ) {}
}
