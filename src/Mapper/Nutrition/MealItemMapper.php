<?php

namespace App\Mapper\Nutrition;

use App\DTO\Request\Nutrition\MealItemRequestDTO;
use App\DTO\Response\Nutrition\MealItemResponseDTO;
use App\Entity\Identity\User;
use App\Entity\Nutrition\MealItem;
use App\Entity\Nutrition\Meal;
use App\Entity\Nutrition\Food;

class MealItemMapper
{
    public function mapRequestToEntity(
        MealItemRequestDTO $dto,
        Meal $meal,
        Food $food,
        ?User $createdBy = null,
        ?MealItem $mealItem = null
    ): MealItem {
        $isNew = $mealItem === null;
        $mealItem ??= new MealItem();

        if ($isNew && $createdBy !== null) {
            $mealItem->setCreatedBy($createdBy);
        }

        $mealItem->setMeal($meal);
        $mealItem->setFood($food);
        $mealItem->setPortionGrams($dto->portionGrams);
        $mealItem->setBreadUnits($dto->breadUnits);

        return $mealItem;
    }

    public function mapEntityToResponse(MealItem $mealItem): MealItemResponseDTO
    {
        return MealItemResponseDTO::fromEntity($mealItem);
    }
}
