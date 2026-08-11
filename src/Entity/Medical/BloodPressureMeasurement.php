<?php

namespace App\Entity\Medical;

use App\Entity\Common\BaseEntity;
use App\Entity\Common\PatientCommonOperation;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une mesure de la pression artérielle d'un patient (tension artérielle et pouls).
 */
#[ORM\Entity]
#[ORM\Table(name: 'medical_blood_pressure_measurements')]
class BloodPressureMeasurement extends PatientCommonOperation
{
    /**
     * @var string|null La valeur de la pression systolique.
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $systolic = null;

    /**
     * @var string|null La valeur de la pression diastolique.
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $diastolic = null;

    /**
     * @var string|null La fréquence du pouls (optionnelle).
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $pulse = null;

    /**
     * Récupère la pression systolique.
     */
    public function getSystolic(): ?string
    {
        return $this->systolic;
    }

    /**
     * Définit la pression systolique.
     */
    public function setSystolic(string $systolic): static
    {
        $this->systolic = $systolic;
        return $this;
    }

    /**
     * Récupère la pression diastolique.
     */
    public function getDiastolic(): ?string
    {
        return $this->diastolic;
    }

    /**
     * Définit la pression diastolique.
     */
    public function setDiastolic(string $diastolic): static
    {
        $this->diastolic = $diastolic;
        return $this;
    }

    /**
     * Récupère la valeur du pouls.
     */
    public function getPulse(): ?string
    {
        return $this->pulse;
    }

    /**
     * Définit la valeur du pouls.
     */
    public function setPulse(?string $pulse): static
    {
        $this->pulse = $pulse;
        return $this;
    }
}
