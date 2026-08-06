<?php

namespace App\Entity\Nutrition;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\HealthcareProfessional;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'foods')]
class Food extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: FoodCategory::class, inversedBy: 'foods')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?FoodCategory $category = null;

    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $photoUrl = null;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    private ?string $caloriesPer100g = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $carbsPer100g = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $proteinPer100g = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $fatPer100g = null;

    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?HealthcareProfessional $createdBy = null;

    public function getCategory(): ?FoodCategory
    {
        return $this->category;
    }

    public function setCategory(?FoodCategory $category): self
    {
        $this->category = $category;
        return $this;
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

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(?string $photoUrl): self
    {
        $this->photoUrl = $photoUrl;
        return $this;
    }

    public function getCaloriesPer100g(): ?string
    {
        return $this->caloriesPer100g;
    }

    public function setCaloriesPer100g(string $caloriesPer100g): self
    {
        $this->caloriesPer100g = $caloriesPer100g;
        return $this;
    }

    public function getCarbsPer100g(): ?string
    {
        return $this->carbsPer100g;
    }

    public function setCarbsPer100g(string $carbsPer100g): self
    {
        $this->carbsPer100g = $carbsPer100g;
        return $this;
    }

    public function getProteinPer100g(): ?string
    {
        return $this->proteinPer100g;
    }

    public function setProteinPer100g(string $proteinPer100g): self
    {
        $this->proteinPer100g = $proteinPer100g;
        return $this;
    }

    public function getFatPer100g(): ?string
    {
        return $this->fatPer100g;
    }

    public function setFatPer100g(string $fatPer100g): self
    {
        $this->fatPer100g = $fatPer100g;
        return $this;
    }

    public function getCreatedBy(): ?HealthcareProfessional
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?HealthcareProfessional $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }
}
