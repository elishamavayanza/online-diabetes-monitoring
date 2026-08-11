<?php

namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un fichier joint (document, image, rapport) lié à une opération
 * ou un dossier de patient au sein du système. Hérite de PatientCommonOperation.
 */
#[ORM\Entity]
#[ORM\Table(name: 'common_file_attachments')]
class FileAttachment extends PatientCommonOperation
{
    /**
     * @var string|null Le nom original ou stocké du fichier.
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $filename = null;

    /**
     * @var string|null Le type MIME du fichier (ex: application/pdf, image/jpeg).
     */
    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private ?string $mimeType = null;

    /**
     * @var int|null La taille du fichier en octets (bytes).
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $sizeBytes = null;

    /**
     * @var string|null Une légende ou une brève description du fichier joint.
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $caption = null;

    /**
     * Récupère le nom du fichier.
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Définit le nom du fichier.
     */
    public function setFilename(string $filename): static
    {
        $this->filename = $filename;
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

    /**
     * Récupère la légende du fichier.
     */
    public function getCaption(): ?string
    {
        return $this->caption;
    }

    /**
     * Définit la légende du fichier.
     */
    public function setCaption(?string $caption): static
    {
        $this->caption = $caption;
        return $this;
    }
}
