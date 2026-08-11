<?php

namespace App\Entity\Common;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Classe mère abstraite (MappedSuperclass) pour toutes les entités du projet.
 * Fournit un identifiant unique auto-incrémenté (BIGINT) ainsi que
 * la gestion automatique des horodatages de création et de mise à jour.
 */
#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class BaseEntity
{
    /**
     * @var string|null L'identifiant unique de l'entité (BIGINT non-signé).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT, options: ['unsigned' => true])]
    protected ?string $id = null;

    /**
     * @var DateTimeImmutable|null La date et l'heure de création de l'enregistrement.
     */
    #[ORM\Column(
        name: 'created_at',
        type: Types::DATETIME_IMMUTABLE,
        nullable: false,
        updatable: false
    )]
    protected ?DateTimeImmutable $createdAt = null;

    /**
     * @var DateTimeImmutable|null La date et l'heure de la dernière modification de l'enregistrement.
     */
    #[ORM\Column(
        name: 'updated_at',
        type: Types::DATETIME_IMMUTABLE,
        nullable: true,
        insertable: false
    )]
    protected ?DateTimeImmutable $updatedAt = null;

    /**
     * Callback exécuté automatiquement avant l'insertion en base de données.
     * Initialise les dates de création et de mise à jour si elles sont nulles.
     */
    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $now = new DateTimeImmutable();

        if ($this->createdAt === null) {
            $this->createdAt = $now;
        }

        if ($this->updatedAt === null) {
            $this->updatedAt = $now;
        }
    }

    /**
     * Callback exécuté automatiquement avant la mise à jour en base de données.
     * Met à jour l'horodatage avec la date et l'heure actuelles.
     */
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Récupère l'identifiant unique de l'entité.
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Récupère la date et l'heure de création.
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Définit manuellement la date et l'heure de création.
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Récupère la date et l'heure de la dernière mise à jour.
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Définit manuellement la date et l'heure de la dernière mise à jour.
     */
    public function setUpdatedAt(?DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
