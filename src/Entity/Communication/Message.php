<?php

namespace App\Entity\Communication;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un message individuel rédigé et envoyé au sein d'une conversation.
 */
#[ORM\Entity]
#[ORM\Table(name: 'communication_messages')]
class Message extends BaseEntity
{
    /**
     * @var Conversation|null Le fil de discussion auquel appartient ce message.
     */
    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Conversation $conversation = null;

    /**
     * @var User|null L'utilisateur (patient ou soignant) qui a rédigé et envoyé le message.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $sender = null;

    /**
     * @var string|null Le contenu textuel du message.
     */
    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure exactes de l'envoi du message.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $sentAt = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure de la dernière modification du message, le cas échéant.
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $editedAt = null;

    /**
     * @var Collection<int, MessageAttachment> La liste des fichiers joints rattachés à ce message.
     */
    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessageAttachment::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $attachments;

    /**
     * Initialise une nouvelle instance du message avec une date d'envoi par défaut.
     */
    public function __construct()
    {
        $this->attachments = new ArrayCollection();
        $this->sentAt = new \DateTimeImmutable();
    }

    /**
     * Récupère la conversation associée au message.
     */
    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    /**
     * Définit la conversation associée au message.
     */
    public function setConversation(?Conversation $conversation): static
    {
        $this->conversation = $conversation;
        return $this;
    }

    /**
     * Récupère l'expéditeur du message.
     */
    public function getSender(): ?User
    {
        return $this->sender;
    }

    /**
     * Définit l'expéditeur du message.
     */
    public function setSender(?User $sender): static
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * Récupère le contenu textuel du message.
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Définit le contenu textuel du message.
     */
    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Récupère la date et l'heure d'envoi.
     */
    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    /**
     * Définit la date et l'heure d'envoi.
     */
    public function setSentAt(\DateTimeImmutable $sentAt): static
    {
        $this->sentAt = $sentAt;
        return $this;
    }

    /**
     * Récupère la date et l'heure de modification.
     */
    public function getEditedAt(): ?\DateTimeImmutable
    {
        return $this->editedAt;
    }

    /**
     * Définit la date et l'heure de modification.
     */
    public function setEditedAt(?\DateTimeImmutable $editedAt): static
    {
        $this->editedAt = $editedAt;
        return $this;
    }

    /**
     * Récupère la collection des pièces jointes.
     *
     * @return Collection<int, MessageAttachment>
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    /**
     * Ajoute une pièce jointe au message.
     */
    public function addAttachment(MessageAttachment $attachment): static
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
            $attachment->setMessage($this);
        }
        return $this;
    }

    /**
     * Supprime une pièce jointe du message.
     */
    public function removeAttachment(MessageAttachment $attachment): static
    {
        if ($this->attachments->removeElement($attachment)) {
            if ($attachment->getMessage() === $this) {
                $attachment->setMessage(null);
            }
        }
        return $this;
    }
}
