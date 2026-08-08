<?php

namespace App\Mapper\Nutrition;

use App\DTO\Request\Nutrition\MealRequestDTO;
use App\DTO\Response\Nutrition\MealResponseDTO;
use App\Entity\Nutrition\Meal;
use App\Entity\Nutrition\MealType;

class MealMapper
{
    public function mapRequestToEntity(MealRequestDTO $dto, ?Meal $meal = null): Meal
    {
        $meal ??= new Meal();

        $meal->setName($dto->name);
        $meal->setDescription($dto->description);

        if ($dto->mealType !== null) {
            $meal->setMealType(is_string($dto->mealType) ? MealType::tryFrom($dto->mealType) : $dto->mealType);
        }

        return $meal;
    }

    public function mapEntityToResponse(Meal $meal): MealResponseDTO
    {
        return MealResponseDTO::fromEntity($meal);
    }
}
