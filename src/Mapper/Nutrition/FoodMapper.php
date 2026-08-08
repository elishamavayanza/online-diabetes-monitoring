<?php

namespace App\Mapper\Nutrition;

use App\DTO\Request\Nutrition\FoodRequestDTO;
use App\DTO\Response\Nutrition\FoodResponseDTO;
use App\Entity\Nutrition\Food;
use App\Entity\Nutrition\FoodCategory;
use App\Entity\Identity\User;

class FoodMapper
{
    public function mapRequestToEntity(
        FoodRequestDTO $dto,
        FoodCategory $category,
        ?User $createdBy = null,
        ?Food $food = null
    ): Food {
        $food ??= new Food();

        $food->setCategory($category);
        $food->setName($dto->name);
        $food->setDescription($dto->description);
        $food->setPhotoUrl($dto->photoUrl);
        $food->setCaloriesPer100g($dto->caloriesPer100g);
        $food->setCarbsPer100g($dto->carbsPer100g);
        $food->setProteinPer100g($dto->proteinPer100g);
        $food->setFatPer100g($dto->fatPer100g);

        if ($createdBy !== null) {
            $food->setCreatedBy($createdBy);
        }

        return $food;
    }

    public function mapEntityToResponse(Food $food): FoodResponseDTO
    {
        return FoodResponseDTO::fromEntity($food);
    }
}
