<?php

namespace App\Entity\Medical;

use App\Entity\Common\PatientCommonOperation;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'hba1c_measurements')]
class HbA1cMeasurement extends PatientCommonOperation
{
    #[ORM\Column(type: 'decimal', precision: 4, scale: 2)]
    private ?string $valuePercent = null;

    public function getValuePercent(): ?string
    {
        return $this->valuePercent;
    }

    public function setValuePercent(string $valuePercent): self
    {
        $this->valuePercent = $valuePercent;
        return $this;
    }
}
