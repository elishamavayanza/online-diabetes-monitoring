<?php

namespace App\Entity\Notification;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Patient;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'reminder_rules')]
class ReminderRule extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    #[ORM\Column(type: 'string', length: 50, enumType: ReminderTargetType::class)]
    private ?ReminderTargetType $targetType = null;

    #[ORM\Column(type: 'guid', nullable: true)]
    private ?string $relatedEntityId = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $cronExpression = null;

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;
        return $this;
    }

    public function getTargetType(): ?ReminderTargetType
    {
        return $this->targetType;
    }

    public function setTargetType(ReminderTargetType $targetType): self
    {
        $this->targetType = $targetType;
        return $this;
    }

    public function getRelatedEntityId(): ?string
    {
        return $this->relatedEntityId;
    }

    public function setRelatedEntityId(?string $relatedEntityId): self
    {
        $this->relatedEntityId = $relatedEntityId;
        return $this;
    }

    public function getCronExpression(): ?string
    {
        return $this->cronExpression;
    }

    public function setCronExpression(string $cronExpression): self
    {
        $this->cronExpression = $cronExpression;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }
}
