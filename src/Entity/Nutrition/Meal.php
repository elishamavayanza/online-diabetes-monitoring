<?php

namespace App\Entity\Nutrition;

use App\Entity\Common\BaseEntity;
use App\Entity\Common\PatientCommonOperation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un repas composé de plusieurs éléments ou aliments.
 */
#[ORM\Entity]
#[ORM\Table(name: 'nutrition_meals')]
class Meal extends PatientCommonOperation
{
    /**
     * @var string|null Le nom du repas.
     */
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    /**
     * @var string|null La description détaillée du repas.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /**
     * @var MealType|null Le type de repas (petit-déjeuner, déjeuner, dîner, etc.).
     */
    #[ORM\Column(type: 'string', length: 50, enumType: MealType::class)]
    private ?MealType $mealType = null;

    /**
     * @var Collection<int, MealItem> La collection des éléments composant ce repas.
     */
    #[ORM\OneToMany(mappedBy: 'meal', targetEntity: MealItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $mealItems;

    /**
     * Constructeur pour initialiser la collection des éléments du repas.
     */
    public function __construct()
    {
        parent::__construct();
        $this->mealItems = new ArrayCollection();
    }

    /**
     * Récupère le nom du repas.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom du repas.
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Récupère la description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description.
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Récupère le type de repas.
     */
    public function getMealType(): ?MealType
    {
        return $this->mealType;
    }

    /**
     * Définit le type de repas.
     */
    public function setMealType(MealType $mealType): static
    {
        $this->mealType = $mealType;
        return $this;
    }

    /**
     * Récupère la collection des éléments du repas.
     *
     * @return Collection<int, MealItem>
     */
    public function getMealItems(): Collection
    {
        return $this->mealItems;
    }

    /**
     * Ajoute un élément au repas.
     */
    public function addMealItem(MealItem $mealItem): static
    {
        if (!$this->mealItems->contains($mealItem)) {
            $this->mealItems->add($mealItem);
            $mealItem->setMeal($this);
        }
        return $this;
    }

    /**
     * Retire un élément du repas.
     */
    public function removeMealItem(MealItem $mealItem): static
    {
        if ($this->mealItems->removeElement($mealItem)) {
            if ($mealItem->getMeal() === $this) {
                $mealItem->setMeal(null);
            }
        }
        return $this;
    }
}
