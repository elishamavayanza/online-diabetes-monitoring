<?php

namespace App\Entity\Healthcare;

use App\Entity\Common\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'healthcare_departments')]
class Department extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: HealthcareFacility::class, inversedBy: 'departments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareFacility $facility = null;

    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $specialty = null;

    public function getFacility(): ?HealthcareFacility
    {
        return $this->facility;
    }

    public function setFacility(?HealthcareFacility $facility): self
    {
        $this->facility = $facility;
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

    public function getSpecialty(): ?string
    {
        return $this->specialty;
    }

    public function setSpecialty(?string $specialty): self
    {
        $this->specialty = $specialty;
        return $this;
    }
}
