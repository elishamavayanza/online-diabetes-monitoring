<?php

namespace App\Entity\Patient;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Patient;
use App\Entity\Healthcare\HealthcareOrganization;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un consentement médical accordé par un patient.
 */
#[ORM\Entity]
#[ORM\Table(name: 'patient_medical_consents')]
class MedicalConsent extends BaseEntity
{
    /**
     * @var Patient|null Le patient ayant accordé le consentement.
     */
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    /**
     * @var HealthcareOrganization|null L'organisation de santé liée au consentement (optionnelle).
     */
    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?HealthcareOrganization $organization = null;

    /**
     * @var ConsentType|null Le type de consentement accordé.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: ConsentType::class)]
    private ?ConsentType $consentType = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure à laquelle le consentement a été accordé.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $grantedAt = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure à laquelle le consentement a été révoqué (optionnel).
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $revokedAt = null;

    /**
     * @var string|null L'URL ou le chemin d'accès vers le document justificatif du consentement.
     */
    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $documentUrl = null;

    /**
     * Récupère le patient associé.
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * Définit le patient associé.
     */
    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    /**
     * Récupère l'organisation de santé associée.
     */
    public function getOrganization(): ?HealthcareOrganization
    {
        return $this->organization;
    }

    /**
     * Définit l'organisation de santé associée.
     */
    public function setOrganization(?HealthcareOrganization $organization): static
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * Récupère le type de consentement.
     */
    public function getConsentType(): ?ConsentType
    {
        return $this->consentType;
    }

    /**
     * Définit le type de consentement.
     */
    public function setConsentType(ConsentType $consentType): static
    {
        $this->consentType = $consentType;
        return $this;
    }

    /**
     * Récupère la date d'octroi du consentement.
     */
    public function getGrantedAt(): ?\DateTimeImmutable
    {
        return $this->grantedAt;
    }

    /**
     * Définit la date d'octroi du consentement.
     */
    public function setGrantedAt(\DateTimeImmutable $grantedAt): static
    {
        $this->grantedAt = $grantedAt;
        return $this;
    }

    /**
     * Récupère la date de révocation du consentement.
     */
    public function getRevokedAt(): ?\DateTimeImmutable
    {
        return $this->revokedAt;
    }

    /**
     * Définit la date de révocation du consentement.
     */
    public function setRevokedAt(?\DateTimeImmutable $revokedAt): static
    {
        $this->revokedAt = $revokedAt;
        return $this;
    }

    /**
     * Récupère l'URL du document.
     */
    public function getDocumentUrl(): ?string
    {
        return $this->documentUrl;
    }

    /**
     * Définit l'URL du document.
     */
    public function setDocumentUrl(?string $documentUrl): static
    {
        $this->documentUrl = $documentUrl;
        return $this;
    }
}
