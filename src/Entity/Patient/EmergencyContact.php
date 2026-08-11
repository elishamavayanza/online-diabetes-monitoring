<?php

namespace App\Entity\Patient;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Patient;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un contact d'urgence associé à un patient.
 */
#[ORM\Entity]
#[ORM\Table(name: 'patient_emergency_contacts')]
class EmergencyContact extends BaseEntity
{
    /**
     * @var Patient|null Le patient auquel est rattaché ce contact d'urgence.
     */
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    /**
     * @var string|null Le nom complet du contact d'urgence.
     */
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $fullName = null;

    /**
     * @var string|null Le lien de parenté ou la relation avec le patient (ex: Conjoint, Parent, Ami).
     */
    #[ORM\Column(type: 'string', length: 100)]
    private ?string $relationship = null;

    /**
     * @var string|null Le numéro de téléphone du contact d'urgence.
     */
    #[ORM\Column(type: 'string', length: 50)]
    private ?string $phone = null;

    /**
     * @var string|null L'adresse e-mail du contact d'urgence (optionnelle).
     */
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    private ?string $email = null;

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
     * Récupère le nom complet.
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * Définit le nom complet.
     */
    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * Récupère la relation avec le patient.
     */
    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    /**
     * Définit la relation avec le patient.
     */
    public function setRelationship(string $relationship): static
    {
        $this->relationship = $relationship;
        return $this;
    }

    /**
     * Récupère le numéro de téléphone.
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Définit le numéro de téléphone.
     */
    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Récupère l'adresse e-mail.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'adresse e-mail.
     */
    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }
}
