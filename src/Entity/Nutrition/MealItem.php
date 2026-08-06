<?php

namespace App\Entity\Nutrition;

use App\Entity\Common\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'meal_items')]
class MealItem extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Meal::class, inversedBy: 'mealItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Meal $meal = null;

    #[ORM\ManyToOne(targetEntity: Food::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Food $food = null;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    private ?string $portionGrams = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $breadUnits = null;

    public function getMeal(): ?Meal
    {
        return $this->meal;
    }

    public function setMeal(?Meal $meal): self
    {
        $this->meal = $meal;
        return $this;
    }

    public function getFood(): ?Food
    {
        return $this->food;
    }

    public function setFood(?Food $food): self
    {
        $this->food = $food;
        return $this;
    }

    public function getPortionGrams(): ?string
    {
        return $this->portionGrams;
    }

    public function setPortionGrams(string $portionGrams): self
    {
        $this->portionGrams = $portionGrams;
        return $this;
    }

    public function getBreadUnits(): ?string
    {
        return $this->breadUnits;
    }

    public function setBreadUnits(?string $breadUnits): self
    {
        $this->breadUnits = $breadUnits;
        return $this;
    }
}
