<?php

namespace App\Entity\Identity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'patients')]
class Patient extends User
{
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $placeOfBirth = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $bloodType = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $heightCm = null;

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    public function getPlaceOfBirth(): ?string
    {
        return $this->placeOfBirth;
    }

    public function setPlaceOfBirth(?string $placeOfBirth): self
    {
        $this->placeOfBirth = $placeOfBirth;
        return $this;
    }

    public function getBloodType(): ?string
    {
        return $this->bloodType;
    }

    public function setBloodType(?string $bloodType): self
    {
        $this->bloodType = $bloodType;
        return $this;
    }

    public function getHeightCm(): ?string
    {
        return $this->heightCm;
    }

    public function setHeightCm(?string $heightCm): self
    {
        $this->heightCm = $heightCm;
        return $this;
    }

    public function getRoles(): array
    {
        return [Role::ROLE_PATIENT->value];
    }
}
