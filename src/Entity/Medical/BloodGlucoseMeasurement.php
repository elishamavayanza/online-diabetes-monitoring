<?php

namespace App\Entity\Medical;

use App\Entity\Common\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'blood_glucose_measurements')]
class BloodGlucoseMeasurement extends BaseEntity
{
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $value = null;

    #[ORM\Column(type: 'string', length: 50, enumType: GlucoseUnit::class)]
    private ?GlucoseUnit $unit = null;

    #[ORM\Column(type: 'string', length: 50, enumType: GlucoseContext::class)]
    private ?GlucoseContext $context = null;

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getUnit(): ?GlucoseUnit
    {
        return $this->unit;
    }

    public function setUnit(GlucoseUnit $unit): self
    {
        $this->unit = $unit;
        return $this;
    }

    public function getContext(): ?GlucoseContext
    {
        return $this->context;
    }

    public function setContext(GlucoseContext $context): self
    {
        $this->context = $context;
        return $this;
    }
}
