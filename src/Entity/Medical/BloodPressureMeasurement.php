<?php

namespace App\Entity\Medical;

use App\Entity\Common\BaseEntity;
use App\Entity\Common\PatientCommonOperation;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'blood_pressure_measurements')]
class BloodPressureMeasurement extends PatientCommonOperation
{
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $systolic = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $diastolic = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $pulse = null;

    public function getSystolic(): ?string
    {
        return $this->systolic;
    }

    public function setSystolic(string $systolic): self
    {
        $this->systolic = $systolic;
        return $this;
    }

    public function getDiastolic(): ?string
    {
        return $this->diastolic;
    }

    public function setDiastolic(string $diastolic): self
    {
        $this->diastolic = $diastolic;
        return $this;
    }

    public function getPulse(): ?string
    {
        return $this->pulse;
    }

    public function setPulse(?string $pulse): self
    {
        $this->pulse = $pulse;
        return $this;
    }
}
