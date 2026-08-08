<?php

namespace App\Mapper\Nutrition;

use App\DTO\Request\Nutrition\FoodCategoryRequestDTO;
use App\DTO\Response\Nutrition\FoodCategoryResponseDTO;
use App\Entity\Nutrition\FoodCategory;

class FoodCategoryMapper
{
    public function mapRequestToEntity(FoodCategoryRequestDTO $dto, ?FoodCategory $category = null): FoodCategory
    {
        $category ??= new FoodCategory();

        $category->setLabel($dto->label);
        $category->setDescription($dto->description);

        return $category;
    }

    public function mapEntityToResponse(FoodCategory $category): FoodCategoryResponseDTO
    {
        return FoodCategoryResponseDTO::fromEntity($category);
    }
}
