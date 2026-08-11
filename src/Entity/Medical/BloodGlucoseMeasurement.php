<?php

namespace App\Entity\Medical;

use App\Entity\Common\BaseEntity;
use App\Entity\Common\PatientCommonOperation;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une mesure de la glycémie sanguine d'un patient.
 */
#[ORM\Entity]
#[ORM\Table(name: 'medical_blood_glucose_measurements')]
class BloodGlucoseMeasurement extends PatientCommonOperation
{
    /**
     * @var string|null La valeur de la glycémie mesurée.
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $value = null;

    /**
     * @var GlucoseUnit|null L'unité de mesure de la glycémie (ex: mg/dL, mmol/L).
     */
    #[ORM\Column(type: 'string', length: 50, enumType: GlucoseUnit::class)]
    private ?GlucoseUnit $unit = null;

    /**
     * @var GlucoseContext|null Le contexte de la mesure (ex: à jeun, post-prandial).
     */
    #[ORM\Column(type: 'string', length: 50, enumType: GlucoseContext::class)]
    private ?GlucoseContext $context = null;

    /**
     * Récupère la valeur de la mesure.
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Définit la valeur de la mesure.
     */
    public function setValue(string $value): static
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Récupère l'unité de mesure.
     */
    public function getUnit(): ?GlucoseUnit
    {
        return $this->unit;
    }

    /**
     * Définit l'unité de mesure.
     */
    public function setUnit(GlucoseUnit $unit): static
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * Récupère le contexte de la mesure.
     */
    public function getContext(): ?GlucoseContext
    {
        return $this->context;
    }

    /**
     * Définit le contexte de la mesure.
     */
    public function setContext(GlucoseContext $context): static
    {
        $this->context = $context;
        return $this;
    }
}
