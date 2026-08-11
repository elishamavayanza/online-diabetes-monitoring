<?php

namespace App\Entity\Nutrition;

use App\Entity\Common\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une catégorie d'aliments.
 */
#[ORM\Entity]
#[ORM\Table(name: 'nutrition_food_categories')]
class FoodCategory extends BaseEntity
{
    /**
     * @var string|null Le libellé ou le nom de la catégorie.
     */
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $label = null;

    /**
     * @var string|null La description détaillée de la catégorie.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Food> La collection des aliments appartenant à cette catégorie.
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Food::class)]
    private Collection $foods;

    /**
     * Constructeur pour initialiser la collection d'aliments.
     */
    public function __construct()
    {
        parent::__construct();
        $this->foods = new ArrayCollection();
    }

    /**
     * Récupère le libellé de la catégorie.
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Définit le libellé de la catégorie.
     */
    public function setLabel(string $label): static
    {
        $this->label = $label;
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
     * Récupère la collection des aliments de la catégorie.
     *
     * @return Collection<int, Food>
     */
    public function getFoods(): Collection
    {
        return $this->foods;
    }

    /**
     * Ajoute un aliment à la catégorie.
     */
    public function addFood(Food $food): static
    {
        if (!$this->foods->contains($food)) {
            $this->foods->add($food);
            $food->setCategory($this);
        }
        return $this;
    }

    /**
     * Retire un aliment de la catégorie.
     */
    public function removeFood(Food $food): static
    {
        if ($this->foods->removeElement($food)) {
            if ($food->getCategory() === $this) {
                $food->setCategory(null);
            }
        }
        return $this;
    }
}
