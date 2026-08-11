<?php

namespace App\Entity\Healthcare;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Patient;
use App\Entity\Identity\HealthcareProfessional;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente l'affectation d'un professionnel de santé à l'équipe de soins
 * d'un patient au sein d'une organisation, avec un rôle et une période déterminés.
 */
#[ORM\Entity]
#[ORM\Table(name: 'healthcare_care_team_assignments')]
class CareTeamAssignment extends BaseEntity
{
    /**
     * @var Patient|null Le patient dont fait partie l'équipe de soins.
     */
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    /**
     * @var HealthcareProfessional|null Le professionnel de santé affecté.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareProfessional $professional = null;

    /**
     * @var HealthcareOrganization|null L'organisation de santé dans le cadre de laquelle l'affectation a lieu.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareOrganization $organization = null;

    /**
     * @var CareTeamRole|null Le rôle du professionnel au sein de l'équipe de soins.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: CareTeamRole::class)]
    private ?CareTeamRole $role = null;

    /**
     * @var \DateTimeInterface|null La date de début de l'affectation.
     */
    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $startDate = null;

    /**
     * @var \DateTimeInterface|null La date de fin de l'affectation (null si toujours en cours).
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    /**
     * @var bool Indique si l'affectation est active ou non.
     */
    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

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
     * Récupère le professionnel de santé associé.
     */
    public function getProfessional(): ?HealthcareProfessional
    {
        return $this->professional;
    }

    /**
     * Définit le professionnel de santé associé.
     */
    public function setProfessional(?HealthcareProfessional $professional): static
    {
        $this->professional = $professional;
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
     * Récupère le rôle dans l'équipe de soins.
     */
    public function getRole(): ?CareTeamRole
    {
        return $this->role;
    }

    /**
     * Définit le rôle dans l'équipe de soins.
     */
    public function setRole(CareTeamRole $role): static
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Récupère la date de début de l'affectation.
     */
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * Définit la date de début de l'affectation.
     */
    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * Récupère la date de fin de l'affectation.
     */
    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    /**
     * Définit la date de fin de l'affectation.
     */
    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * Indique si l'affectation est active.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Modifie l'état d'activité de l'affectation.
     */
    public function setActive(bool $active): static
    {
        $this->active = $active;
        return $this;
    }
}
