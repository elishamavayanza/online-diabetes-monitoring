<?php

namespace App\Entity\Healthcare;

use App\Entity\Common\BaseEntity;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Identity\Patient;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trace une action effectuée par un professionnel externe invité
 * sur le dossier d'un patient (audit du suivi).
 */
#[ORM\Entity]
#[ORM\Table(name: 'external_follow_logs')]
class ExternalFollowLog extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareProfessional $professional = null;

    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareOrganization $organization = null;

    #[ORM\ManyToOne(targetEntity: ExternalFollowInvitation::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?ExternalFollowInvitation $invitation = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $action = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $actionLabel = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $detail = null;

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    public function getProfessional(): ?HealthcareProfessional
    {
        return $this->professional;
    }

    public function setProfessional(?HealthcareProfessional $professional): static
    {
        $this->professional = $professional;
        return $this;
    }

    public function getOrganization(): ?HealthcareOrganization
    {
        return $this->organization;
    }

    public function setOrganization(?HealthcareOrganization $organization): static
    {
        $this->organization = $organization;
        return $this;
    }

    public function getInvitation(): ?ExternalFollowInvitation
    {
        return $this->invitation;
    }

    public function setInvitation(?ExternalFollowInvitation $invitation): static
    {
        $this->invitation = $invitation;
        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): static
    {
        $this->action = $action;
        return $this;
    }

    public function getActionLabel(): ?string
    {
        return $this->actionLabel;
    }

    public function setActionLabel(?string $actionLabel): static
    {
        $this->actionLabel = $actionLabel;
        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): static
    {
        $this->detail = $detail;
        return $this;
    }
}