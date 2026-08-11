<?php

namespace App\Entity\Treatment;

use App\Entity\Common\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un médicament avec ses caractéristiques et sa catégorie.
 */
#[ORM\Entity]
#[ORM\Table(name: 'treatment_medications')]
class Medication extends BaseEntity
{
    /**
     * @var string|null Le nom du médicament.
     */
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    /**
     * @var MedicationCategory|null La catégorie du médicament.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: MedicationCategory::class)]
    private ?MedicationCategory $category = null;

    /**
     * @var string|null La description détaillée du médicament.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /**
     * @var int|null Le niveau d'insuline ou la concentration associée (le cas échéant).
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $insulinLevel = null;

    /**
     * @var string|null Le fabricant ou le laboratoire pharmaceutique.
     */
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $manufacturer = null;

    /**
     * Récupère le nom du médicament.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom du médicament.
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Récupère la catégorie du médicament.
     */
    public function getCategory(): ?MedicationCategory
    {
        return $this->category;
    }

    /**
     * Définit la catégorie du médicament.
     */
    public function setCategory(MedicationCategory $category): static
    {
        $this->category = $category;
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
     * Récupère le niveau d'insuline.
     */
    public function getInsulinLevel(): ?int
    {
        return $this->insulinLevel;
    }

    /**
     * Définit le niveau d'insuline.
     */
    public function setInsulinLevel(?int $insulinLevel): static
    {
        $this->insulinLevel = $insulinLevel;
        return $this;
    }

    /**
     * Récupère le fabricant.
     */
    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    /**
     * Définit le fabricant.
     */
    public function setManufacturer(?string $manufacturer): static
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }
}
