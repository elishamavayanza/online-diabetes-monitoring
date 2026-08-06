<?php

namespace App\Entity\Nutrition;

use App\Entity\Common\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'meals')]
class Meal extends BaseEntity
{
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 50, enumType: MealType::class)]
    private ?MealType $mealType = null;

    #[ORM\OneToMany(mappedBy: 'meal', targetEntity: MealItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $mealItems;

    public function __construct()
    {
        parent::__construct();
        $this->mealItems = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getMealType(): ?MealType
    {
        return $this->mealType;
    }

    public function setMealType(MealType $mealType): self
    {
        $this->mealType = $mealType;
        return $this;
    }

    /**
     * @return Collection<int, MealItem>
     */
    public function getMealItems(): Collection
    {
        return $this->mealItems;
    }

    public function addMealItem(MealItem $mealItem): self
    {
        if (!$this->mealItems->contains($mealItem)) {
            $this->mealItems->add($mealItem);
            $mealItem->setMeal($this);
        }
        return $this;
    }

    public function removeMealItem(MealItem $mealItem): self
    {
        if ($this->mealItems->removeElement($mealItem)) {
            if ($mealItem->getMeal() === $this) {
                $mealItem->setMeal(null);
            }
        }
        return $this;
    }
}
