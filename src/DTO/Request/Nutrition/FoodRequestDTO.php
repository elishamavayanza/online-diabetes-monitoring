<?php

namespace App\DTO\Request\Nutrition;

use Symfony\Component\Validator\Constraints as Assert;

class FoodRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $categoryId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        public readonly string $name,

        #[Assert\Length(max: 5000)]
        public readonly ?string $description,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        public readonly ?string $photoUrl,

        #[Assert\NotBlank]
        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        public readonly string $caloriesPer100g,

        #[Assert\NotBlank]
        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        public readonly string $carbsPer100g,

        #[Assert\NotBlank]
        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        public readonly string $proteinPer100g,

        #[Assert\NotBlank]
        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        public readonly string $fatPer100g,

        public readonly ?string $createdById
    ) {}
}
