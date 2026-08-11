<?php

namespace App\Entity\Communication;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\User;
use App\Entity\Healthcare\HealthcareOrganization;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un fil de discussion médical unique centré sur un patient
 * et partagé avec son équipe soignante au sein de la plateforme.
 */
#[ORM\Entity]
#[ORM\Table(name: 'communication_conversations')]
class Conversation extends BaseEntity
{
    /**
     * @var string|null L'objet ou le titre optionnel de la conversation.
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $subject = null;

    /**
     * @var User|null Le patient concerné par ce fil de discussion médical.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $patient = null;

    /**
     * @var HealthcareOrganization|null L'organisation de santé rattachée à cette conversation.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?HealthcareOrganization $organization = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure de clôture de la conversation, le cas échéant.
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $closedAt = null;

    /**
     * @var Collection<int, Message> La liste des messages échangés dans ce fil de discussion.
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'conversation', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $messages;

    /**
     * Initialise une nouvelle instance de la conversation et sa collection de messages.
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    /**
     * Récupère l'objet de la conversation.
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * Définit l'objet de la conversation.
     */
    public function setSubject(?string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Récupère le patient associé à la conversation.
     */
    public function getPatient(): ?User
    {
        return $this->patient;
    }

    /**
     * Définit le patient associé à la conversation.
     */
    public function setPatient(?User $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    /**
     * Récupère l'organisation de santé associée.
     */
    public function getOrganization(): ?HealthcareOrganization
    {
        return $this->organization;
    }

    /**
     * Définit l'organisation de santé associée.
     */
    public function setOrganization(?HealthcareOrganization $organization): static
    {
        $this->organization = $organization;
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
     * Récupère l'ensemble des messages du fil de discussion.
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
     * Supprime un message de la conversation.
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
