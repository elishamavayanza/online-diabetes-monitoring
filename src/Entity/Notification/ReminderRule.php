<?php

namespace App\Entity\Notification;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Patient;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une règle de rappel programmée pour un patient.
 */
#[ORM\Entity]
#[ORM\Table(name: 'notification_reminder_rules')]
class ReminderRule extends BaseEntity
{
    /**
     * @var Patient|null Le patient concerné par la règle de rappel.
     */
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    /**
     * @var ReminderTargetType|null Le type de cible du rappel.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: ReminderTargetType::class)]
    private ?ReminderTargetType $targetType = null;

    /**
     * @var string|null L'identifiant (guid) de l'entité liée.
     */
    #[ORM\Column(type: 'guid', nullable: true)]
    private ?string $relatedEntityId = null;

    /**
     * @var string|null L'expression CRON définissant la planification du rappel.
     */
    #[ORM\Column(type: 'string', length: 100)]
    private ?string $cronExpression = null;

    /**
     * @var bool Indique si la règle de rappel est active.
     */
    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    /**
     * Récupère le patient associé.
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * Définit le patient associé.
     */
    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    /**
     * Récupère le type de cible.
     */
    public function getTargetType(): ?ReminderTargetType
    {
        return $this->targetType;
    }

    /**
     * Définit le type de cible.
     */
    public function setTargetType(ReminderTargetType $targetType): static
    {
        $this->targetType = $targetType;
        return $this;
    }

    /**
     * Récupère l'ID de l'entité liée.
     */
    public function getRelatedEntityId(): ?string
    {
        return $this->relatedEntityId;
    }

    /**
     * Définit l'ID de l'entité liée.
     */
    public function setRelatedEntityId(?string $relatedEntityId): static
    {
        $this->relatedEntityId = $relatedEntityId;
        return $this;
    }

    /**
     * Récupère l'expression CRON.
     */
    public function getCronExpression(): ?string
    {
        return $this->cronExpression;
    }

    /**
     * Définit l'expression CRON.
     */
    public function setCronExpression(string $cronExpression): static
    {
        $this->cronExpression = $cronExpression;
        return $this;
    }

    /**
     * Indique si la règle est active.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Définit si la règle est active.
     */
    public function setActive(bool $active): static
    {
        $this->active = $active;
        return $this;
    }
}
