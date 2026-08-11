<?php

namespace App\Entity\Healthcare;

use App\Entity\Common\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un département ou un service médical au sein d'une structure de santé.
 */
#[ORM\Entity]
#[ORM\Table(name: 'healthcare_departments')]
class Department extends BaseEntity
{
    /**
     * @var HealthcareFacility|null L'établissement de santé auquel ce département est rattaché.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareFacility::class, inversedBy: 'departments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareFacility $facility = null;

    /**
     * @var string|null Le nom du département ou du service (ex: Cardiologie, Urgences).
     */
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    /**
     * @var string|null La spécialité médicale principale exercée au sein de ce département.
     */
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $specialty = null;

    /**
     * Récupère l'établissement de santé associé.
     */
    public function getFacility(): ?HealthcareFacility
    {
        return $this->facility;
    }

    /**
     * Définit l'établissement de santé associé.
     */
    public function setFacility(?HealthcareFacility $facility): static
    {
        $this->facility = $facility;
        return $this;
    }

    /**
     * Récupère le nom du département.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom du département.
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Récupère la spécialité médicale.
     */
    public function getSpecialty(): ?string
    {
        return $this->specialty;
    }

    /**
     * Définit la spécialité médicale.
     */
    public function setSpecialty(?string $specialty): static
    {
        $this->specialty = $specialty;
        return $this;
    }
}
