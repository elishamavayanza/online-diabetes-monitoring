<?php

namespace App\Entity\Medical;

use App\Entity\Common\BaseEntity;
use App\Entity\Common\PatientCommonOperation;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'physical_activity_measurements')]
class PhysicalActivityMeasurement extends PatientCommonOperation
{
    #[ORM\Column(type: 'string', length: 100)]
    private ?string $activityType = null;

    #[ORM\Column(type: 'integer')]
    private ?int $durationMinutes = null;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2, nullable: true)]
    private ?string $caloriesBurned = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $minHeartRate = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $maxHeartRate = null;

    public function getActivityType(): ?string
    {
        return $this->activityType;
    }

    public function setActivityType(string $activityType): self
    {
        $this->activityType = $activityType;
        return $this;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(int $durationMinutes): self
    {
        $this->durationMinutes = $durationMinutes;
        return $this;
    }

    public function getCaloriesBurned(): ?string
    {
        return $this->caloriesBurned;
    }

    public function setCaloriesBurned(?string $caloriesBurned): self
    {
        $this->caloriesBurned = $caloriesBurned;
        return $this;
    }

    public function getMinHeartRate(): ?string
    {
        return $this->minHeartRate;
    }

    public function setMinHeartRate(?string $minHeartRate): self
    {
        $this->minHeartRate = $minHeartRate;
        return $this;
    }

    public function getMaxHeartRate(): ?string
    {
        return $this->maxHeartRate;
    }

    public function setMaxHeartRate(?string $maxHeartRate): self
    {
        $this->maxHeartRate = $maxHeartRate;
        return $this;
    }
}
