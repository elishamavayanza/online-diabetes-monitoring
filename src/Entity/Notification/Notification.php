<?php

namespace App\Entity\Notification;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\User;
use App\Entity\Appointment\ReminderChannel;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une notification système envoyée à un utilisateur.
 */
#[ORM\Entity]
#[ORM\Table(name: 'notification_notifications')]
class Notification extends BaseEntity
{
    /**
     * @var User|null L'utilisateur destinataire de la notification.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    /**
     * @var NotificationType|null Le type de notification.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: NotificationType::class)]
    private ?NotificationType $type = null;

    /**
     * @var string|null Le titre de la notification.
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = null;

    /**
     * @var string|null Le contenu ou corps de la notification.
     */
    #[ORM\Column(type: 'text')]
    private ?string $body = null;

    /**
     * @var ReminderChannel|null Le canal par lequel la notification a été envoyée.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: ReminderChannel::class)]
    private ?ReminderChannel $channel = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure à laquelle la notification a été lue.
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $readAt = null;

    /**
     * @var string|null Le type d'entité liée à la notification.
     */
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $relatedEntityType = null;

    /**
     * @var string|null L'identifiant (GUID) de l'entité liée.
     */
    #[ORM\Column(type: 'guid', nullable: true)]
    private ?string $relatedEntityId = null;

    /**
     * Récupère l'utilisateur destinataire.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Définit l'utilisateur destinataire.
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Récupère le type de notification.
     */
    public function getType(): ?NotificationType
    {
        return $this->type;
    }

    /**
     * Définit le type de notification.
     */
    public function setType(NotificationType $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Récupère le titre.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Définit le titre.
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Récupère le corps de la notification.
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * Définit le corps de la notification.
     */
    public function setBody(string $body): static
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Récupère le canal d'envoi.
     */
    public function getChannel(): ?ReminderChannel
    {
        return $this->channel;
    }

    /**
     * Définit le canal d'envoi.
     */
    public function setChannel(ReminderChannel $channel): static
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * Récupère la date de lecture.
     */
    public function getReadAt(): ?\DateTimeImmutable
    {
        return $this->readAt;
    }

    /**
     * Définit la date de lecture.
     */
    public function setReadAt(?\DateTimeImmutable $readAt): static
    {
        $this->readAt = $readAt;
        return $this;
    }

    /**
     * Récupère le type d'entité liée.
     */
    public function getRelatedEntityType(): ?string
    {
        return $this->relatedEntityType;
    }

    /**
     * Définit le type d'entité liée.
     */
    public function setRelatedEntityType(?string $relatedEntityType): static
    {
        $this->relatedEntityType = $relatedEntityType;
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
}
