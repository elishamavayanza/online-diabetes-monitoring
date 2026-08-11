<?php

namespace App\Entity\Medical;

use App\Entity\Common\BaseEntity;
use App\Entity\Common\PatientCommonOperation;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une mesure d'activité physique effectuée par un patient.
 */
#[ORM\Entity]
#[ORM\Table(name: 'medical_physical_activity_measurements')]
class PhysicalActivityMeasurement extends PatientCommonOperation
{
    /**
     * @var string|null Le type d'activité physique (ex: marche, course, natation).
     */
    #[ORM\Column(type: 'string', length: 100)]
    private ?string $activityType = null;

    /**
     * @var int|null La durée de l'activité en minutes.
     */
    #[ORM\Column(type: 'integer')]
    private ?int $durationMinutes = null;

    /**
     * @var string|null Le nombre de calories brûlées (optionnel).
     */
    #[ORM\Column(type: 'decimal', precision: 6, scale: 2, nullable: true)]
    private ?string $caloriesBurned = null;

    /**
     * @var string|null La fréquence cardiaque minimale durant l'activité (optionnelle).
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $minHeartRate = null;

    /**
     * @var string|null La fréquence cardiaque maximale durant l'activité (optionnelle).
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $maxHeartRate = null;

    /**
     * Récupère le type d'activité.
     */
    public function getActivityType(): ?string
    {
        return $this->activityType;
    }

    /**
     * Définit le type d'activité.
     */
    public function setActivityType(string $activityType): static
    {
        $this->activityType = $activityType;
        return $this;
    }

    /**
     * Récupère la durée en minutes.
     */
    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    /**
     * Définit la durée en minutes.
     */
    public function setDurationMinutes(int $durationMinutes): static
    {
        $this->durationMinutes = $durationMinutes;
        return $this;
    }

    /**
     * Récupère les calories brûlées.
     */
    public function getCaloriesBurned(): ?string
    {
        return $this->caloriesBurned;
    }

    /**
     * Définit les calories brûlées.
     */
    public function setCaloriesBurned(?string $caloriesBurned): static
    {
        $this->caloriesBurned = $caloriesBurned;
        return $this;
    }

    /**
     * Récupère la fréquence cardiaque minimale.
     */
    public function getMinHeartRate(): ?string
    {
        return $this->minHeartRate;
    }

    /**
     * Définit la fréquence cardiaque minimale.
     */
    public function setMinHeartRate(?string $minHeartRate): static
    {
        $this->minHeartRate = $minHeartRate;
        return $this;
    }

    /**
     * Récupère la fréquence cardiaque maximale.
     */
    public function getMaxHeartRate(): ?string
    {
        return $this->maxHeartRate;
    }

    /**
     * Définit la fréquence cardiaque maximale.
     */
    public function setMaxHeartRate(?string $maxHeartRate): static
    {
        $this->maxHeartRate = $maxHeartRate;
        return $this;
    }
}
