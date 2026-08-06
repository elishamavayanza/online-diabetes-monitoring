<?php

namespace App\DTO\Request\Nutrition;

use Symfony\Component\Validator\Constraints as Assert;

class MealRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        public readonly string $name,

        #[Assert\Length(max: 5000)]
        public readonly ?string $description,

        #[Assert\NotBlank]
        public readonly mixed $mealType
    ) {}
}
