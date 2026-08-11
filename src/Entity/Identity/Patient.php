<?php

namespace App\Entity\Identity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un patient héritant de l'entité User,
 * avec ses informations de naissance, son groupe sanguin, sa taille et son rôle spécifique.
 */
#[ORM\Entity]
#[ORM\Table(name: 'identity_patients')]
class Patient extends User
{
    /**
     * @var \DateTimeInterface|null La date de naissance du patient.
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateOfBirth = null;

    /**
     * @var string|null Le lieu de naissance du patient.
     */
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $placeOfBirth = null;

    /**
     * @var string|null Le groupe sanguin du patient (ex: A+, O-, etc.).
     */
    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $bloodType = null;

    /**
     * @var string|null La taille du patient en centimètres.
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $heightCm = null;

    /**
     * Récupère la date de naissance.
     */
    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    /**
     * Définit la date de naissance.
     */
    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    /**
     * Récupère le lieu de naissance.
     */
    public function getPlaceOfBirth(): ?string
    {
        return $this->placeOfBirth;
    }

    /**
     * Définit le lieu de naissance.
     */
    public function setPlaceOfBirth(?string $placeOfBirth): static
    {
        $this->placeOfBirth = $placeOfBirth;
        return $this;
    }

    /**
     * Récupère le groupe sanguin.
     */
    public function getBloodType(): ?string
    {
        return $this->bloodType;
    }

    /**
     * Définit le groupe sanguin.
     */
    public function setBloodType(?string $bloodType): static
    {
        $this->bloodType = $bloodType;
        return $this;
    }

    /**
     * Récupère la taille en centimètres.
     */
    public function getHeightCm(): ?string
    {
        return $this->heightCm;
    }

    /**
     * Définit la taille en centimètres.
     */
    public function setHeightCm(?string $heightCm): static
    {
        $this->heightCm = $heightCm;
        return $this;
    }

    /**
     * Retourne les rôles de sécurité attribués au patient.
     *
     * @return array<int, string>
     */
    public function getRoles(): array
    {
        return [Role::ROLE_PATIENT->value];
    }
}
