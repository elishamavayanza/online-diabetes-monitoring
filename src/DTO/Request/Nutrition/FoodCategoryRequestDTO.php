<?php

namespace App\DTO\Request\Nutrition;

use Symfony\Component\Validator\Constraints as Assert;

class FoodCategoryRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        public readonly string $label,

        #[Assert\Length(max: 5000)]
        public readonly ?string $description
    ) {}
}
