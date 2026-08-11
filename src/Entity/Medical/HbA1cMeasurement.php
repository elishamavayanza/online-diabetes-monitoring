<?php

namespace App\Entity\Medical;

use App\Entity\Common\PatientCommonOperation;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une mesure du taux d'hémoglobine glyquée (HbA1c) d'un patient.
 */
#[ORM\Entity]
#[ORM\Table(name: 'medical_hba1c_measurements')]
class HbA1cMeasurement extends PatientCommonOperation
{
    /**
     * @var string|null La valeur du pourcentage d'HbA1c.
     */
    #[ORM\Column(type: 'decimal', precision: 4, scale: 2)]
    private ?string $valuePercent = null;

    /**
     * Récupère la valeur en pourcentage.
     */
    public function getValuePercent(): ?string
    {
        return $this->valuePercent;
    }

    /**
     * Définit la valeur en pourcentage.
     */
    public function setValuePercent(string $valuePercent): static
    {
        $this->valuePercent = $valuePercent;
        return $this;
    }
}
