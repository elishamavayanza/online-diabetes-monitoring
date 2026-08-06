<?php

namespace App\Entity\Audit;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\User;
use App\Entity\Identity\Patient;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'data_access_logs')]
class DataAccessLog extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?User $accessedBy = null;

    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    #[ORM\Column(type: 'string', length: 150)]
    private ?string $resourceType = null;

    #[ORM\Column(type: 'guid')]
    private ?string $resourceId = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $accessedAt = null;

    public function getAccessedBy(): ?User
    {
        return $this->accessedBy;
    }

    public function setAccessedBy(?User $accessedBy): self
    {
        $this->accessedBy = $accessedBy;
        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;
        return $this;
    }

    public function getResourceType(): ?string
    {
        return $this->resourceType;
    }

    public function setResourceType(string $resourceType): self
    {
        $this->resourceType = $resourceType;
        return $this;
    }

    public function getResourceId(): ?string
    {
        return $this->resourceId;
    }

    public function setResourceId(string $resourceId): self
    {
        $this->resourceId = $resourceId;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;
        return $this;
    }

    public function getAccessedAt(): ?\DateTimeImmutable
    {
        return $this->accessedAt;
    }

    public function setAccessedAt(\DateTimeImmutable $accessedAt): self
    {
        $this->accessedAt = $accessedAt;
        return $this;
    }
}
