<?php

namespace App\Entity\Communication;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\User;
use App\Entity\Healthcare\HealthcareOrganization;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un fil de discussion ou une conversation entre plusieurs participants
 * au sein du module de communication.
 */
#[ORM\Entity]
#[ORM\Table(name: 'communication_conversations')]
class Conversation extends BaseEntity
{
    /**
     * @var string|null Sujet principal ou titre de la conversation.
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $subject = null;

    /**
     * @var HealthcareOrganization|null L'organisation de santé dans le cadre de laquelle la conversation se tient.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?HealthcareOrganization $organization = null;

    /**
     * @var User|null L'utilisateur qui a initié la conversation.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?User $createdBy = null;

    /**
     * @var \DateTimeImmutable|null Date et heure de fermeture de la conversation (null si active).
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $closedAt = null;

    /**
     * @var Collection<int, ConversationParticipant> Liste des participants à cette conversation.
     */
    #[ORM\OneToMany(targetEntity: ConversationParticipant::class, mappedBy: 'conversation', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $participants;

    /**
     * @var Collection<int, Message> Liste des messages échangés dans cette conversation.
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'conversation', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $messages;

    /**
     * Initialise les collections de participants et de messages.
     */
    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    /**
     * Récupère le sujet de la conversation.
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * Définit le sujet de la conversation.
     */
    public function setSubject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Récupère l'organisation de santé rattachée.
     */
    public function getOrganization(): ?HealthcareOrganization
    {
        return $this->organization;
    }

    /**
     * Définit l'organisation de santé rattachée.
     */
    public function setOrganization(?HealthcareOrganization $organization): static
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * Récupère le créateur de la conversation.
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * Définit le créateur de la conversation.
     */
    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * Récupère la date de clôture de la conversation.
     */
    public function getClosedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    /**
     * Définit la date de clôture de la conversation.
     */
    public function setClosedAt(?\DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;
        return $this;
    }

    /**
     * Retourne la liste des participants.
     *
     * @return Collection<int, ConversationParticipant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    /**
     * Ajoute un participant à la conversation.
     */
    public function addParticipant(ConversationParticipant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->setConversation($this);
        }
        return $this;
    }

    /**
     * Retire un participant de la conversation.
     */
    public function removeParticipant(ConversationParticipant $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            if ($participant->getConversation() === $this) {
                $participant->setConversation(null);
            }
        }
        return $this;
    }

    /**
     * Retourne la liste des messages de la conversation.
     *
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    /**
     * Ajoute un message à la conversation.
     */
    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setConversation($this);
        }
        return $this;
    }

    /**
     * Retire un message de la conversation.
     */
    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }
        return $this;
    }
}
