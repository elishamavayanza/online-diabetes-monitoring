<?php

namespace App\Entity\Communication;

use App\Entity\Common\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un fichier joint rattaché à un message dans le module de communication.
 */
#[ORM\Entity]
#[ORM\Table(name: 'communication_message_attachments')]
class MessageAttachment extends BaseEntity
{
    /**
     * @var Message|null Le message auquel cette pièce jointe est rattachée.
     */
    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: 'attachments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Message $message = null;

    /**
     * @var string|null L'URL, le chemin d'accès ou l'identifiant de stockage du fichier.
     */
    #[ORM\Column(type: 'string', length: 500)]
    private ?string $fileUrl = null;

    /**
     * @var string|null Le nom original du fichier.
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $fileName = null;

    /**
     * @var string|null Le type MIME du fichier (ex: application/pdf, image/png).
     */
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $mimeType = null;

    /**
     * @var int|null La taille du fichier en octets (bytes).
     */
    #[ORM\Column(type: 'integer')]
    private ?int $sizeBytes = null;

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
     * Récupère l'URL ou le chemin du fichier.
     */
    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    /**
     * Définit l'URL ou le chemin du fichier.
     */
    public function setFileUrl(string $fileUrl): static
    {
        $this->fileUrl = $fileUrl;
        return $this;
    }

    /**
     * Récupère le nom du fichier.
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * Définit le nom du fichier.
     */
    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Récupère le type MIME du fichier.
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * Définit le type MIME du fichier.
     */
    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * Récupère la taille du fichier en octets.
     */
    public function getSizeBytes(): ?int
    {
        return $this->sizeBytes;
    }

    /**
     * Définit la taille du fichier en octets.
     */
    public function setSizeBytes(int $sizeBytes): static
    {
        $this->sizeBytes = $sizeBytes;
        return $this;
    }
}
