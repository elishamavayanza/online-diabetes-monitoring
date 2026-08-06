<?php

namespace App\Entity\Treatment;

use App\Entity\Common\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'medications')]
class Medication extends BaseEntity
{
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 50, enumType: MedicationCategory::class)]
    private ?MedicationCategory $category = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $insulinLevel = null;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $manufacturer = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCategory(): ?MedicationCategory
    {
        return $this->category;
    }

    public function setCategory(MedicationCategory $category): self
    {
        $this->category = $category;
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

    public function getInsulinLevel(): ?int
    {
        return $this->insulinLevel;
    }

    public function setInsulinLevel(?int $insulinLevel): self
    {
        $this->insulinLevel = $insulinLevel;
        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }
}
