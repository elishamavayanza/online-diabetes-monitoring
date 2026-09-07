<?php

namespace App\Entity\Nutrition;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un élément ou composant individuel d'un repas (un aliment et sa portion).
 */
#[ORM\Entity]
#[ORM\Table(name: 'nutrition_meal_items')]
class MealItem extends BaseEntity
{
    /**
     * @var Meal|null Le repas auquel appartient cet élément.
     */
    #[ORM\ManyToOne(targetEntity: Meal::class, inversedBy: 'mealItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Meal $meal = null;

    /**
     * @var Food|null L'aliment consommé.
     */
    #[ORM\ManyToOne(targetEntity: Food::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Food $food = null;

    /**
     * @var string|null La quantité de la portion en grammes.
     */
    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    private ?string $portionGrams = null;

    /**
     * @var string|null Le nombre d'unités pain (UP) équivalentes.
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $breadUnits = null;

    /**
     * @var User|null L'utilisateur qui a créé cet élément de repas.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    /**
     * Récupère le repas associé.
     */
    public function getMeal(): ?Meal
    {
        return $this->meal;
    }

    /**
     * Définit le repas associé.
     */
    public function setMeal(?Meal $meal): static
    {
        $this->meal = $meal;
        return $this;
    }

    /**
     * Récupère l'aliment.
     */
    public function getFood(): ?Food
    {
        return $this->food;
    }

    /**
     * Définit l'aliment.
     */
    public function setFood(?Food $food): static
    {
        $this->food = $food;
        return $this;
    }

    /**
     * Récupère la portion en grammes.
     */
    public function getPortionGrams(): ?string
    {
        return $this->portionGrams;
    }

    /**
     * Définit la portion en grammes.
     */
    public function setPortionGrams(string $portionGrams): static
    {
        $this->portionGrams = $portionGrams;
        return $this;
    }

    /**
     * Récupère les unités pain.
     */
    public function getBreadUnits(): ?string
    {
        return $this->breadUnits;
    }

    /**
     * Définit les unités pain.
     */
    public function setBreadUnits(?string $breadUnits): static
    {
        $this->breadUnits = $breadUnits;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }
}
