<?php

namespace App\Entity\Identity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'identity_healthcare_professionals')]
class HealthcareProfessional extends User
{
    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private ?string $licenseNumber = null;

    #[ORM\Column(type: 'string', length: 50, enumType: ProfessionalType::class)]
    private ?ProfessionalType $professionalType = null;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $specialty = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $signatureUrl = null;

    public function getLicenseNumber(): ?string
    {
        return $this->licenseNumber;
    }

    public function setLicenseNumber(string $licenseNumber): self
    {
        $this->licenseNumber = $licenseNumber;
        return $this;
    }

    public function getProfessionalType(): ?ProfessionalType
    {
        return $this->professionalType;
    }

    public function setProfessionalType(ProfessionalType $professionalType): self
    {
        $this->professionalType = $professionalType;
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

    public function getSignatureUrl(): ?string
    {
        return $this->signatureUrl;
    }

    public function setSignatureUrl(?string $signatureUrl): self
    {
        $this->signatureUrl = $signatureUrl;
        return $this;
    }

    public function getRoles(): array
    {
        return match ($this->professionalType) {
            ProfessionalType::CLINICIAN => [Role::ROLE_CLINICIAN->value],
            ProfessionalType::NUTRITIONIST => [Role::ROLE_NUTRITIONIST->value],
            default => [],
        };
    }
}
