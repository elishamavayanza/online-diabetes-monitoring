<?php

namespace App\Entity\Communication;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un message individuel échangé au sein d'une conversation
 * dans le module de communication.
 */
#[ORM\Entity]
#[ORM\Table(name: 'communication_messages')]
class Message extends BaseEntity
{
    /**
     * @var Conversation|null La conversation à laquelle appartient ce message.
     */
    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Conversation $conversation = null;

    /**
     * @var User|null L'utilisateur qui a rédigé et envoyé le message.
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
     * @var \DateTimeImmutable|null La date et l'heure de la dernière modification du message (null si non modifié).
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $editedAt = null;

    /**
     * @var Collection<int, MessageAttachment> Les fichiers joints rattachés à ce message.
     */
    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessageAttachment::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $attachments;

    /**
     * @var Collection<int, MessageReadReceipt> Les accusés de lecture associés à ce message.
     */
    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessageReadReceipt::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $readReceipts;

    /**
     * Initialise les collections de pièces jointes et d'accusés de lecture.
     */
    public function __construct()
    {
        $this->attachments = new ArrayCollection();
        $this->readReceipts = new ArrayCollection();
    }

    /**
     * Récupère la conversation associée.
     */
    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    /**
     * Définit la conversation associée.
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
     * Récupère la date de dernière modification.
     */
    public function getEditedAt(): ?\DateTimeImmutable
    {
        return $this->editedAt;
    }

    /**
     * Définit la date de dernière modification.
     */
    public function setEditedAt(?\DateTimeImmutable $editedAt): static
    {
        $this->editedAt = $editedAt;
        return $this;
    }

    /**
     * Retourne la collection des pièces jointes.
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
     * Retire une pièce jointe du message.
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

    /**
     * Retourne la collection des accusés de lecture.
     *
     * @return Collection<int, MessageReadReceipt>
     */
    public function getReadReceipts(): Collection
    {
        return $this->readReceipts;
    }

    /**
     * Ajoute un accusé de lecture au message.
     */
    public function addReadReceipt(MessageReadReceipt $readReceipt): static
    {
        if (!$this->readReceipts->contains($readReceipt)) {
            $this->readReceipts->add($readReceipt);
            $readReceipt->setMessage($this);
        }
        return $this;
    }

    /**
     * Retire un accusé de lecture du message.
     */
    public function removeReadReceipt(MessageReadReceipt $readReceipt): static
    {
        if ($this->readReceipts->removeElement($readReceipt)) {
            if ($readReceipt->getMessage() === $this) {
                $readReceipt->setMessage(null);
            }
        }
        return $this;
    }
}
