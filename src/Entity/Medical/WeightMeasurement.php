<?php

namespace App\Entity\Medical;

use App\Entity\Common\PatientCommonOperation;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'weight_measurements')]
class WeightMeasurement extends PatientCommonOperation
{
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $valueKg = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $heightCm = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $bmi = null;

    public function getValueKg(): ?string
    {
        return $this->valueKg;
    }

    public function setValueKg(string $valueKg): self
    {
        $this->valueKg = $valueKg;
        $this->calculateBmi();
        return $this;
    }

    public function getHeightCm(): ?string
    {
        return $this->heightCm;
    }

    public function setHeightCm(?string $heightCm): self
    {
        $this->heightCm = $heightCm;
        $this->calculateBmi();
        return $this;
    }

    public function getBmi(): ?string
    {
        return $this->bmi;
    }

    public function setBmi(?string $bmi): self
    {
        $this->bmi = $bmi;
        return $this;
    }

    private function calculateBmi(): void
    {
        if ($this->valueKg !== null && $this->heightCm !== null && $this->heightCm > 0) {
            $heightM = (float)$this->heightCm / 100;
            $bmiValue = (float)$this->valueKg / ($heightM * $heightM);
            $this->bmi = number_format($bmiValue, 2, '.', '');
        }
    }
}
