<?php

namespace App\Entity\Identity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un professionnel de santé héritant de l'entité User,
 * avec son numéro de licence, son type, sa spécialité et sa signature électronique.
 */
#[ORM\Entity]
#[ORM\Table(name: 'identity_healthcare_professionals')]
class HealthcareProfessional extends User
{
    /**
     * @var string|null Le numéro de licence ou d'enregistrement professionnel (unique).
     */
    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private ?string $licenseNumber = null;

    /**
     * @var ProfessionalType|null Le type de professionnel de santé (ex: clinicien, nutritionniste).
     */
    #[ORM\Column(type: 'string', length: 50, enumType: ProfessionalType::class)]
    private ?ProfessionalType $professionalType = null;

    /**
     * @var string|null La spécialité médicale ou professionnelle.
     */
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $specialty = null;

    /**
     * @var string|null L'URL ou le chemin d'accès vers la signature numérique du professionnel.
     */
    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $signatureUrl = null;

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
    public function setLicenseNumber(string $licenseNumber): static
    {
        $this->licenseNumber = $licenseNumber;
        return $this;
    }

    /**
     * Récupère le type de professionnel de santé.
     */
    public function getProfessionalType(): ?ProfessionalType
    {
        return $this->professionalType;
    }

    /**
     * Définit le type de professionnel de santé.
     */
    public function setProfessionalType(ProfessionalType $professionalType): static
    {
        $this->professionalType = $professionalType;
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
     * Retourne les rôles de sécurité attribués en fonction du type de professionnel.
     *
     * @return array<int, string>
     */
    public function getRoles(): array
    {
        return match ($this->professionalType) {
            ProfessionalType::CLINICIAN => [Role::ROLE_CLINICIAN->value],
            ProfessionalType::NUTRITIONIST => [Role::ROLE_NUTRITIONIST->value],
            default => [],
        };
    }
}
