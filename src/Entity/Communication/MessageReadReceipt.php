<?php

namespace App\Entity\Communication;

use App\Entity\Common\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente l'accusé de lecture d'un message par un participant
 * au sein du module de communication.
 */
#[ORM\Entity]
#[ORM\Table(name: 'communication_message_read_receipts')]
class MessageReadReceipt extends BaseEntity
{
    /**
     * @var Message|null Le message concerné par cet accusé de lecture.
     */
    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: 'readReceipts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Message $message = null;

    /**
     * @var ConversationParticipant|null Le participant qui a lu le message.
     */
    #[ORM\ManyToOne(targetEntity: ConversationParticipant::class, inversedBy: 'readReceipts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ConversationParticipant $participant = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure exactes de la lecture du message.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $readAt = null;

    /**
     * Récupère le message associé.
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * Définit le message associé.
     */
    public function setMessage(?Message $message): static
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Récupère le participant associé.
     */
    public function getParticipant(): ?ConversationParticipant
    {
        return $this->participant;
    }

    /**
     * Définit le participant associé.
     */
    public function setParticipant(?ConversationParticipant $participant): static
    {
        $this->participant = $participant;
        return $this;
    }

    /**
     * Récupère la date et l'heure de lecture.
     */
    public function getReadAt(): ?\DateTimeImmutable
    {
        return $this->readAt;
    }

    /**
     * Définit la date et l'heure de lecture.
     */
    public function setReadAt(\DateTimeImmutable $readAt): static
    {
        $this->readAt = $readAt;
        return $this;
    }
}
