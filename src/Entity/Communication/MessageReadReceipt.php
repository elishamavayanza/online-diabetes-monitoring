<?php

namespace App\Entity\Communication;

use App\Entity\Common\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'message_read_receipts')]
class MessageReadReceipt extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: 'readReceipts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Message $message = null;

    #[ORM\ManyToOne(targetEntity: ConversationParticipant::class, inversedBy: 'readReceipts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ConversationParticipant $participant = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $readAt = null;

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getParticipant(): ?ConversationParticipant
    {
        return $this->participant;
    }

    public function setParticipant(?ConversationParticipant $participant): self
    {
        $this->participant = $participant;
        return $this;
    }

    public function getReadAt(): ?\DateTimeImmutable
    {
        return $this->readAt;
    }

    public function setReadAt(\DateTimeImmutable $readAt): self
    {
        $this->readAt = $readAt;
        return $this;
    }
}
