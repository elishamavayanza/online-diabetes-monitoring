<?php

namespace App\Entity\Communication;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente la participation d'un utilisateur à une conversation
 * au sein du module de communication.
 */
#[ORM\Entity]
#[ORM\Table(name: 'communication_conversation_participants')]
class ConversationParticipant extends BaseEntity
{
    /**
     * @var Conversation|null La conversation à laquelle l'utilisateur participe.
     */
    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Conversation $conversation = null;

    /**
     * @var User|null L'utilisateur concerné par cette participation.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure à lesquelles l'utilisateur a rejoint la conversation.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $joinedAt = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure à lesquelles l'utilisateur a quitté la conversation (null si toujours actif).
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $leftAt = null;

    /**
     * @var Collection<int, MessageReadReceipt> Les accusés de lecture associés à ce participant.
     */
    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: MessageReadReceipt::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $readReceipts;

    /**
     * Initialise la collection des accusés de lecture.
     */
    public function __construct()
    {
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
     * Récupère l'utilisateur participant.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Définit l'utilisateur participant.
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Récupère la date d'adhésion à la conversation.
     */
    public function getJoinedAt(): ?\DateTimeImmutable
    {
        return $this->joinedAt;
    }

    /**
     * Définit la date d'adhésion à la conversation.
     */
    public function setJoinedAt(\DateTimeImmutable $joinedAt): static
    {
        $this->joinedAt = $joinedAt;
        return $this;
    }

    /**
     * Récupère la date de sortie de la conversation.
     */
    public function getLeftAt(): ?\DateTimeImmutable
    {
        return $this->leftAt;
    }

    /**
     * Définit la date de sortie de la conversation.
     */
    public function setLeftAt(?\DateTimeImmutable $leftAt): static
    {
        $this->leftAt = $leftAt;
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
     * Ajoute un accusé de lecture pour ce participant.
     */
    public function addReadReceipt(MessageReadReceipt $readReceipt): static
    {
        if (!$this->readReceipts->contains($readReceipt)) {
            $this->readReceipts->add($readReceipt);
            $readReceipt->setParticipant($this);
        }
        return $this;
    }

    /**
     * Retire un accusé de lecture de ce participant.
     */
    public function removeReadReceipt(MessageReadReceipt $readReceipt): static
    {
        if ($this->readReceipts->removeElement($readReceipt)) {
            if ($readReceipt->getParticipant() === $this) {
                $readReceipt->setParticipant(null);
            }
        }
        return $this;
    }
}
