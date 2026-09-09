<?php

namespace App\Entity\Security;

use App\Entity\Common\BaseEntity;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\User;
use App\Repository\Security\AccountSuspensionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Suspension d'une organisation ou d'un compte (professionnel / patient).
 *
 * - Scope ORGANIZATION : l'organisation est désactivée, tous ses membres sont
 *   bloqués à la connexion et sur les requêtes API tant que la suspension court.
 * - Scope PROFESSIONAL / PATIENT : seul le compte ciblé est suspendu, avec son
 *   adhésion (membership) à l'organisation suspendue.
 *
 * La levée se fait manuellement (réactivation) ou automatiquement quand endsAt
 * est atteint (commande app:suspensions:process-expired).
 */
#[ORM\Entity(repositoryClass: AccountSuspensionRepository::class)]
#[ORM\Table(name: 'account_suspensions')]
#[ORM\Index(name: 'idx_suspension_scope_dates', columns: ['scope', 'canceled_at', 'ends_at'])]
class AccountSuspension extends BaseEntity
{
    #[ORM\Column(type: Types::STRING, length: 20, enumType: SuspensionScope::class)]
    private SuspensionScope $scope;

    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(name: 'organization_id', nullable: true, onDelete: 'SET NULL')]
    private ?HealthcareOrganization $organization = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(type: Types::STRING, length: 500)]
    private ?string $reason = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $startsAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $endsAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $canceledAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_id', nullable: true, onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    public function __construct()
    {
        $this->scope = SuspensionScope::ORGANIZATION;
        $this->startsAt = new \DateTimeImmutable();
    }

    public function getScope(): SuspensionScope
    {
        return $this->scope;
    }

    public function setScope(SuspensionScope $scope): static
    {
        $this->scope = $scope;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getStartsAt(): \DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function setStartsAt(\DateTimeImmutable $startsAt): static
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): ?\DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(?\DateTimeImmutable $endsAt): static
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    public function getCanceledAt(): ?\DateTimeImmutable
    {
        return $this->canceledAt;
    }

    public function setCanceledAt(?\DateTimeImmutable $canceledAt): static
    {
        $this->canceledAt = $canceledAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function isActiveAt(\DateTimeImmutable $now): bool
    {
        return $this->canceledAt === null
            && ($this->endsAt === null || $this->endsAt >= $now);
    }
}