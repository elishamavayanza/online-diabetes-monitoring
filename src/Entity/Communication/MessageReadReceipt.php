<?php

namespace App\Entity\Communication;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente l'accusé de lecture d'un message par un utilisateur spécifique
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
     * @var User|null L'utilisateur (soignant ou patient) qui a lu le message.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure exactes de la lecture du message.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $readAt = null;

    /**
     * Récupère le message associé à l'accusé de lecture.
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * Définit le message associé à l'accusé de lecture.
     */
    public function setMessage(?Message $message): static
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Récupère l'utilisateur qui a lu le message.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Définit l'utilisateur qui a lu le message.
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;
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
