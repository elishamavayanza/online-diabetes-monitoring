<?php

namespace App\Entity\Communication;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'conversation_participants')]
class ConversationParticipant extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Conversation $conversation = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $joinedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $leftAt = null;

    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: MessageReadReceipt::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $readReceipts;

    public function __construct()
    {
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getJoinedAt(): ?\DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(\DateTimeImmutable $joinedAt): self
    {
        $this->joinedAt = $joinedAt;
        return $this;
    }

    public function getLeftAt(): ?\DateTimeImmutable
    {
        return $this->leftAt;
    }

    public function setLeftAt(?\DateTimeImmutable $leftAt): self
    {
        $this->leftAt = $leftAt;
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
            $readReceipt->setParticipant($this);
        }
        return $this;
    }

    public function removeReadReceipt(MessageReadReceipt $readReceipt): self
    {
        if ($this->readReceipts->removeElement($readReceipt)) {
            if ($readReceipt->getParticipant() === $this) {
                $readReceipt->setParticipant(null);
            }
        }
        return $this;
    }
}
