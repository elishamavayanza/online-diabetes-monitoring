<?php

namespace App\Entity\Medical;

use App\Entity\Common\PatientCommonOperation;
use App\Entity\Identity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une note médicale rattachée à un dossier médical et rédigée par un utilisateur.
 */
#[ORM\Entity]
#[ORM\Table(name: 'medical_medical_notes')]
class MedicalNote extends PatientCommonOperation
{
    /**
     * @var MedicalRecord|null Le dossier médical associé à la note.
     */
    #[ORM\ManyToOne(targetEntity: MedicalRecord::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?MedicalRecord $medicalRecord = null;

    /**
     * @var User|null L'auteur de la note médicale.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $author = null;

    /**
     * @var string|null Le contenu textuel de la note.
     */
    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure de rédaction de la note.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $notedAt = null;

    /**
     * Récupère le dossier médical associé.
     */
    public function getMedicalRecord(): ?MedicalRecord
    {
        return $this->medicalRecord;
    }

    /**
     * Définit le dossier médical associé.
     */
    public function setMedicalRecord(?MedicalRecord $medicalRecord): static
    {
        $this->medicalRecord = $medicalRecord;
        return $this;
    }

    /**
     * Récupère l'auteur de la note.
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Définit l'auteur de la note.
     */
    public function setAuthor(?User $author): static
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Récupère le contenu de la note.
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Définit le contenu de la note.
     */
    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Récupère la date de la note.
     */
    public function getNotedAt(): ?\DateTimeImmutable
    {
        return $this->notedAt;
    }

    /**
     * Définit la date de la note.
     */
    public function setNotedAt(\DateTimeImmutable $notedAt): static
    {
        $this->notedAt = $notedAt;
        return $this;
    }
}
