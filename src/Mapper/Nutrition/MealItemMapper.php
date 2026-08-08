<?php

namespace App\Mapper\Nutrition;

use App\DTO\Request\Nutrition\MealItemRequestDTO;
use App\DTO\Response\Nutrition\MealItemResponseDTO;
use App\Entity\Nutrition\MealItem;
use App\Entity\Nutrition\Meal;
use App\Entity\Nutrition\Food;

class MealItemMapper
{
    public function mapRequestToEntity(
        MealItemRequestDTO $dto,
        Meal $meal,
        Food $food,
        ?MealItem $mealItem = null
    ): MealItem {
        $mealItem ??= new MealItem();

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
