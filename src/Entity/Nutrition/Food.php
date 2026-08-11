<?php

namespace App\Entity\Nutrition;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\HealthcareProfessional;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un aliment avec ses informations nutritionnelles pour 100g.
 */
#[ORM\Entity]
#[ORM\Table(name: 'nutrition_foods')]
class Food extends BaseEntity
{
    /**
     * @var FoodCategory|null La catégorie à laquelle appartient l'aliment.
     */
    #[ORM\ManyToOne(targetEntity: FoodCategory::class, inversedBy: 'foods')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?FoodCategory $category = null;

    /**
     * @var string|null Le nom de l'aliment.
     */
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    /**
     * @var string|null La description détaillée de l'aliment.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /**
     * @var string|null L'URL ou le chemin d'accès de la photo de l'aliment.
     */
    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $photoUrl = null;

    /**
     * @var string|null Le nombre de calories pour 100g.
     */
    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    private ?string $caloriesPer100g = null;

    /**
     * @var string|null La quantité de glucides en grammes pour 100g.
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $carbsPer100g = null;

    /**
     * @var string|null La quantité de protéines en grammes pour 100g.
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $proteinPer100g = null;

    /**
     * @var string|null La quantité de lipides en grammes pour 100g.
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $fatPer100g = null;

    /**
     * @var HealthcareProfessional|null Le professionnel de santé ayant créé ou validé cet aliment.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?HealthcareProfessional $createdBy = null;

    /**
     * Récupère la catégorie de l'aliment.
     */
    public function getCategory(): ?FoodCategory
    {
        return $this->category;
    }

    /**
     * Définit la catégorie de l'aliment.
     */
    public function setCategory(?FoodCategory $category): static
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Récupère le nom de l'aliment.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de l'aliment.
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
     * Récupère l'URL de la photo.
     */
    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    /**
     * Définit l'URL de la photo.
     */
    public function setPhotoUrl(?string $photoUrl): static
    {
        $this->photoUrl = $photoUrl;
        return $this;
    }

    /**
     * Récupère les calories pour 100g.
     */
    public function getCaloriesPer100g(): ?string
    {
        return $this->caloriesPer100g;
    }

    /**
     * Définit les calories pour 100g.
     */
    public function setCaloriesPer100g(string $caloriesPer100g): static
    {
        $this->caloriesPer100g = $caloriesPer100g;
        return $this;
    }

    /**
     * Récupère les glucides pour 100g.
     */
    public function getCarbsPer100g(): ?string
    {
        return $this->carbsPer100g;
    }

    /**
     * Définit les glucides pour 100g.
     */
    public function setCarbsPer100g(string $carbsPer100g): static
    {
        $this->carbsPer100g = $carbsPer100g;
        return $this;
    }

    /**
     * Récupère les protéines pour 100g.
     */
    public function getProteinPer100g(): ?string
    {
        return $this->proteinPer100g;
    }

    /**
     * Définit les protéines pour 100g.
     */
    public function setProteinPer100g(string $proteinPer100g): static
    {
        $this->proteinPer100g = $proteinPer100g;
        return $this;
    }

    /**
     * Récupère les lipides pour 100g.
     */
    public function getFatPer100g(): ?string
    {
        return $this->fatPer100g;
    }

    /**
     * Définit les lipides pour 100g.
     */
    public function setFatPer100g(string $fatPer100g): static
    {
        $this->fatPer100g = $fatPer100g;
        return $this;
    }

    /**
     * Récupère le créateur de l'aliment.
     */
    public function getCreatedBy(): ?HealthcareProfessional
    {
        return $this->createdBy;
    }

    /**
     * Définit le créateur de l'aliment.
     */
    public function setCreatedBy(?HealthcareProfessional $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }
}
