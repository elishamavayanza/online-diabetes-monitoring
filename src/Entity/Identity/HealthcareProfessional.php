<?php

namespace App\Entity\Identity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un professionnel de santé héritant de l'entité User,
 * avec ses informations de licence, son type et sa spécialité.
 */
#[ORM\Entity]
#[ORM\Table(name: 'identity_healthcare_professionals')]
class HealthcareProfessional extends User
{
    /**
     * @var ProfessionalType|null Le type de professionnel de santé.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: ProfessionalType::class)]
    private ?ProfessionalType $professionalType = null;

    /**
     * @var string|null Le numéro de licence ou d'ordre professionnel.
     */
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $licenseNumber = null;

    /**
     * @var string|null La spécialité médicale ou diététique.
     */
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $specialty = null;

    /**
     * @var string|null L'URL ou le chemin vers la signature numérique du professionnel.
     */
    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $signatureUrl = null;

    /**
     * Récupère le type de professionnel.
     */
    public function getProfessionalType(): ?ProfessionalType
    {
        return $this->professionalType;
    }

    /**
     * Définit le type de professionnel.
     */
    public function setProfessionalType(ProfessionalType $professionalType): static
    {
        $this->professionalType = $professionalType;
        return $this;
    }

    /**
     * Récupère le numéro de licence.
     */
    public function getLicenseNumber(): ?string
    {
        return $this->licenseNumber;
    }

    /**
     * Définit le numéro de licence.
     */
    public function setLicenseNumber(?string $licenseNumber): static
    {
        $this->licenseNumber = $licenseNumber;
        return $this;
    }

    /**
     * Récupère la spécialité.
     */
    public function getSpecialty(): ?string
    {
        return $this->specialty;
    }

    /**
     * Définit la spécialité.
     */
    public function setSpecialty(?string $specialty): static
    {
        $this->specialty = $specialty;
        return $this;
    }

    /**
     * Récupère l'URL de la signature.
     */
    public function getSignatureUrl(): ?string
    {
        return $this->signatureUrl;
    }

    /**
     * Définit l'URL de la signature.
     */
    public function setSignatureUrl(?string $signatureUrl): static
    {
        $this->signatureUrl = $signatureUrl;
        return $this;
    }

    /**
     * Retourne les rôles de sécurité attribués au professionnel de santé
     * en fonction de son type.
     *
     * @return array<int, string>
     */
    public function getRoles(): array
    {
        $specificRole = match ($this->professionalType) {
            ProfessionalType::CLINICIAN => Role::ROLE_CLINICIAN->value,
            ProfessionalType::NUTRITIONIST => Role::ROLE_NUTRITIONIST->value,
            default => null,
        };

        $roles = parent::getRoles();

        if ($specificRole && !in_array($specificRole, $roles, true)) {
            $roles[] = $specificRole;
        }

        return array_values(array_unique($roles));
    }
}
