<?php

namespace App\Entity\Medical;

use App\Entity\Common\PatientCommonOperation;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une mesure du poids d'un patient avec calcul automatique de l'IMC (Indice de Masse Corporelle).
 */
#[ORM\Entity]
#[ORM\Table(name: 'medical_weight_measurements')]
class WeightMeasurement extends PatientCommonOperation
{
    /**
     * @var string|null La valeur du poids en kilogrammes.
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $valueKg = null;

    /**
     * @var string|null La taille en centimètres associée à la mesure.
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $heightCm = null;

    /**
     * @var string|null L'indice de masse corporelle (IMC) calculé.
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $bmi = null;

    /**
     * Récupère la valeur du poids en kilogrammes.
     */
    public function getValueKg(): ?string
    {
        return $this->valueKg;
    }

    /**
     * Définit la valeur du poids en kilogrammes et met à jour l'IMC.
     */
    public function setValueKg(string $valueKg): static
    {
        $this->valueKg = $valueKg;
        $this->calculateBmi();
        return $this;
    }

    /**
     * Récupère la taille en centimètres.
     */
    public function getHeightCm(): ?string
    {
        return $this->heightCm;
    }

    /**
     * Définit la taille en centimètres et met à jour l'IMC.
     */
    public function setHeightCm(?string $heightCm): static
    {
        $this->heightCm = $heightCm;
        $this->calculateBmi();
        return $this;
    }

    /**
     * Récupère l'IMC calculé.
     */
    public function getBmi(): ?string
    {
        return $this->bmi;
    }

    /**
     * Définit l'IMC.
     */
    public function setBmi(?string $bmi): static
    {
        $this->bmi = $bmi;
        return $this;
    }

    /**
     * Calcule automatiquement l'Indice de Masse Corporelle (IMC) si le poids et la taille sont valides.
     */
    private function calculateBmi(): void
    {
        if ($this->valueKg !== null && $this->heightCm !== null && (float)$this->heightCm > 0) {
            $heightM = (float)$this->heightCm / 100;
            $bmiValue = (float)$this->valueKg / ($heightM * $heightM);
            $this->bmi = number_format($bmiValue, 2, '.', '');
        }
    }
}
