<?php

namespace App\Entity\Notification;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\User;
use App\Entity\Appointment\ReminderChannel;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'notifications')]
class Notification extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 50, enumType: NotificationType::class)]
    private ?NotificationType $type = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $body = null;

    #[ORM\Column(type: 'string', length: 50, enumType: ReminderChannel::class)]
    private ?ReminderChannel $channel = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $readAt = null;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $relatedEntityType = null;

    #[ORM\Column(type: 'guid', nullable: true)]
    private ?string $relatedEntityId = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getType(): ?NotificationType
    {
        return $this->type;
    }

    public function setType(NotificationType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getChannel(): ?ReminderChannel
    {
        return $this->channel;
    }

    public function setChannel(ReminderChannel $channel): self
    {
        $this->channel = $channel;
        return $this;
    }

    public function getReadAt(): ?\DateTimeImmutable
    {
        return $this->readAt;
    }

    public function setReadAt(?\DateTimeImmutable $readAt): self
    {
        $this->readAt = $readAt;
        return $this;
    }

    public function getRelatedEntityType(): ?string
    {
        return $this->relatedEntityType;
    }

    public function setRelatedEntityType(?string $relatedEntityType): self
    {
        $this->relatedEntityType = $relatedEntityType;
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
}
