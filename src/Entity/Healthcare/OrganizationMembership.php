<?php

namespace App\Entity\Healthcare;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente l'adhésion ou l'appartenance d'un utilisateur à une organisation de santé,
 * avec éventuellement un rattachement à un établissement ou un département spécifique.
 */
#[ORM\Entity]
#[ORM\Table(name: 'healthcare_organization_memberships')]
class OrganizationMembership extends BaseEntity
{
    /**
     * @var User|null L'utilisateur concerné par l'adhésion.
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'organizationMemberships')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    /**
     * @var HealthcareOrganization|null L'organisation de santé rattachée.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class, inversedBy: 'memberships')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareOrganization $organization = null;

    /**
     * @var HealthcareFacility|null L'établissement spécifique de l'organisation (optionnel).
     */
    #[ORM\ManyToOne(targetEntity: HealthcareFacility::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?HealthcareFacility $facility = null;

    /**
     * @var Department|null Le département ou service spécifique au sein de l'établissement (optionnel).
     */
    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Department $department = null;

    /**
     * @var \DateTimeImmutable|null La date de début de l'adhésion.
     */
    #[ORM\Column(type: 'date_immutable')]
    private ?\DateTimeImmutable $startDate = null;

    /**
     * @var \DateTimeImmutable|null La date de fin de l'adhésion (null si toujours active).
     */
    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    /**
     * @var MembershipStatus|null Le statut actuel de l'adhésion.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: MembershipStatus::class)]
    private ?MembershipStatus $status = MembershipStatus::ACTIVE;

    /**
     * Récupère l'utilisateur associé.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Définit l'utilisateur associé.
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Récupère l'organisation associée.
     */
    public function getOrganization(): ?HealthcareOrganization
    {
        return $this->organization;
    }

    /**
     * Définit l'organisation associée.
     */
    public function setOrganization(?HealthcareOrganization $organization): static
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * Récupère l'établissement associé.
     */
    public function getFacility(): ?HealthcareFacility
    {
        return $this->facility;
    }

    /**
     * Définit l'établissement associé.
     */
    public function setFacility(?HealthcareFacility $facility): static
    {
        $this->facility = $facility;
        return $this;
    }

    /**
     * Récupère le département associé.
     */
    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    /**
     * Définit le département associé.
     */
    public function setDepartment(?Department $department): static
    {
        $this->department = $department;
        return $this;
    }

    /**
     * Récupère la date de début de l'adhésion.
     */
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * Définit la date de début de l'adhésion.
     */
    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * Récupère la date de fin de l'adhésion.
     */
    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    /**
     * Définit la date de fin de l'adhésion.
     */
    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * Récupère le statut de l'adhésion.
     */
    public function getStatus(): ?MembershipStatus
    {
        return $this->status;
    }

    /**
     * Définit le statut de l'adhésion.
     */
    public function setStatus(MembershipStatus $status): static
    {
        $this->status = $status;
        return $this;
    }
}
