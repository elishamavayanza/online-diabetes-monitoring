<?php

namespace App\Entity\Medical;

use App\Entity\Common\BaseEntity;
use App\Entity\Common\PatientCommonOperation;
use App\Entity\Identity\Patient;
use App\Entity\Healthcare\HealthcareOrganization;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'medical_records')]
class MedicalRecord extends PatientCommonOperation
{
    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareOrganization $organization = null;

    #[ORM\Column(type: 'string', length: 50, enumType: MedicalRecordStatus::class)]
    private ?MedicalRecordStatus $status = MedicalRecordStatus::OPEN;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $openedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $closedAt = null;

    public function getOrganization(): ?HealthcareOrganization
    {
        return $this->organization;
    }

    public function setOrganization(?HealthcareOrganization $organization): self
    {
        $this->organization = $organization;
        return $this;
    }

    public function getStatus(): ?MedicalRecordStatus
    {
        return $this->status;
    }

    public function setStatus(MedicalRecordStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getOpenedAt(): ?DateTimeImmutable
    {
        return $this->openedAt;
    }

    public function setOpenedAt(DateTimeImmutable $openedAt): self
    {
        $this->openedAt = $openedAt;
        return $this;
    }

    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?DateTimeImmutable $closedAt): self
    {
        $this->closedAt = $closedAt;
        return $this;
    }
}
