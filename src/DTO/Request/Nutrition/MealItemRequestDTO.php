<?php

namespace App\DTO\Request\Nutrition;

use Symfony\Component\Validator\Constraints as Assert;

class MealItemRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $mealId,

        #[Assert\NotBlank]
        public readonly string $foodId,

        #[Assert\NotBlank]
        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        public readonly string $portionGrams,

        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        public readonly ?string $breadUnits
    ) {}
}
