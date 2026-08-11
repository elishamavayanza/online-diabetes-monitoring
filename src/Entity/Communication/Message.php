<?php

namespace App\Entity\Communication;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'communication_messages')]
class Message extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Conversation $conversation = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $sender = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $sentAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $editedAt = null;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessageAttachment::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $attachments;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessageReadReceipt::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $readReceipts;

    public function __construct()
    {
        parent::__construct();
        $this->attachments = new ArrayCollection();
        $this->readReceipts = new ArrayCollection();
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;
        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeImmutable $sentAt): self
    {
        $this->sentAt = $sentAt;
        return $this;
    }

    public function getEditedAt(): ?\DateTimeImmutable
    {
        return $this->editedAt;
    }

    public function setEditedAt(?\DateTimeImmutable $editedAt): self
    {
        $this->editedAt = $editedAt;
        return $this;
    }

    /**
     * @return Collection<int, MessageAttachment>
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(MessageAttachment $attachment): self
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
            $attachment->setMessage($this);
        }
        return $this;
    }

    public function removeAttachment(MessageAttachment $attachment): self
    {
        if ($this->attachments->removeElement($attachment)) {
            if ($attachment->getMessage() === $this) {
                $attachment->setMessage(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, MessageReadReceipt>
     */
    public function getReadReceipts(): Collection
    {
        return $this->readReceipts;
    }

    public function addReadReceipt(MessageReadReceipt $readReceipt): self
    {
        if (!$this->readReceipts->contains($readReceipt)) {
            $this->readReceipts->add($readReceipt);
            $readReceipt->setMessage($this);
        }
        return $this;
    }

    public function removeReadReceipt(MessageReadReceipt $readReceipt): self
    {
        if ($this->readReceipts->removeElement($readReceipt)) {
            if ($readReceipt->getMessage() === $this) {
                $readReceipt->setMessage(null);
            }
        }
        return $this;
    }
}
