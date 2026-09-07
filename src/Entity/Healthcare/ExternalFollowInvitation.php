<?php

namespace App\Entity\Healthcare;

use App\Entity\Common\BaseEntity;
use App\Entity\Healthcare\CareTeamAssignment;
use App\Entity\Healthcare\ExternalFollowStatus;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Identity\Patient;
use App\Entity\Identity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Invitation envoyée par l'administrateur d'une organisation à un professionnel
 * d'une autre organisation afin de suivre un patient pendant une durée définie.
 */
#[ORM\Entity]
#[ORM\Table(name: 'external_follow_invitations')]
class ExternalFollowInvitation extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareOrganization $organization = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'invited_by_id', nullable: true, onDelete: 'SET NULL')]
    private ?User $invitedBy = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: HealthcareProfessional::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareProfessional $professional = null;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private ?string $token = null;

    #[ORM\Column(type: 'string', length: 45, enumType: ExternalFollowStatus::class)]
    private ?ExternalFollowStatus $status = null;

    #[ORM\Column(type: 'date_immutable')]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $message = null;

    #[ORM\OneToOne(targetEntity: CareTeamAssignment::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?CareTeamAssignment $assignment = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $acceptedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $declinedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $revokedAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $closureReason = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $closedByProfessionalAt = null;

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;
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

    public function getInvitedBy(): ?User
    {
        return $this->invitedBy;
    }

    public function setInvitedBy(?User $invitedBy): static
    {
        $this->invitedBy = $invitedBy;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;
        return $this;
    }

    public function getStatus(): ?ExternalFollowStatus
    {
        return $this->status;
    }

    public function setStatus(?ExternalFollowStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getAssignment(): ?CareTeamAssignment
    {
        return $this->assignment;
    }

    public function setAssignment(?CareTeamAssignment $assignment): static
    {
        $this->assignment = $assignment;
        return $this;
    }

    public function getAcceptedAt(): ?\DateTimeImmutable
    {
        return $this->acceptedAt;
    }

    public function setAcceptedAt(?\DateTimeImmutable $acceptedAt): static
    {
        $this->acceptedAt = $acceptedAt;
        return $this;
    }

    public function getDeclinedAt(): ?\DateTimeImmutable
    {
        return $this->declinedAt;
    }

    public function setDeclinedAt(?\DateTimeImmutable $declinedAt): static
    {
        $this->declinedAt = $declinedAt;
        return $this;
    }

    public function getRevokedAt(): ?\DateTimeImmutable
    {
        return $this->revokedAt;
    }

    public function setRevokedAt(?\DateTimeImmutable $revokedAt): static
    {
        $this->revokedAt = $revokedAt;
        return $this;
    }

    public function getClosureReason(): ?string
    {
        return $this->closureReason;
    }

    public function setClosureReason(?string $closureReason): static
    {
        $this->closureReason = $closureReason;
        return $this;
    }

    public function getClosedByProfessionalAt(): ?\DateTimeImmutable
    {
        return $this->closedByProfessionalAt;
    }

    public function setClosedByProfessionalAt(?\DateTimeImmutable $closedByProfessionalAt): static
    {
        $this->closedByProfessionalAt = $closedByProfessionalAt;
        return $this;
    }

    /**
     * Vraie tant que l'invitation est encore utilisable
     * (en attente ou acceptée et non échue).
     */
    public function isActiveWindow(\DateTimeImmutable $today): bool
    {
        if (
            $this->status !== ExternalFollowStatus::PENDING
            && $this->status !== ExternalFollowStatus::ACCEPTED
        ) {
            return false;
        }

        return $this->endDate === null || $this->endDate >= $today;
    }
}