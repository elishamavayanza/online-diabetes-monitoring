<?php

namespace App\Entity\Common;

use App\Entity\Identity\Patient;
use App\Entity\Identity\User;
use App\Repository\Common\PatientCommonOperationRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class PatientCommonOperation extends BaseEntity
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "patient_id", nullable: false)]
    protected ?Patient $patient = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "issuer_id", nullable: false)]
    protected ?User $issuer = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    protected ?DateTimeImmutable $measuredAt = null;


    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;

        return $this;
    }

    public function getIssuer(): ?User
    {
        return $this->issuer;
    }

    public function setIssuer(?User $issuer): static
    {
        $this->issuer = $issuer;

        return $this;
    }

    public function getMeasuredAt(): ?DateTimeImmutable
    {
        return $this->measuredAt;
    }

    public function setMeasuredAt(DateTimeImmutable $measuredAt): static
    {
        $this->measuredAt = $measuredAt;
        return $this;
    }
}
