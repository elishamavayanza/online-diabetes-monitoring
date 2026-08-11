<?php

namespace App\Entity\Identity;

use App\Entity\Common\BaseEntity;
use App\Entity\Common\Gender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Classe abstraite MappedSuperclass représentant une personne physique
 * avec ses informations de base (nom complet, téléphone, genre, avatar et adresse).
 */
#[ORM\MappedSuperclass]
abstract class Person extends BaseEntity
{
    /**
     * @var string|null Le nom complet de la personne.
     */
    #[ORM\Column(type: 'string', length: 150)]
    protected ?string $fullName = null;

    /**
     * @var string|null Le numéro de téléphone de la personne.
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    protected ?string $phone = null;

    /**
     * @var Gender|null Le genre de la personne.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: Gender::class, nullable: true)]
    protected ?Gender $gender = null;

    /**
     * @var string|null L'URL ou le chemin d'accès vers l'avatar/photo de profil.
     */
    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    protected ?string $avatarUrl = null;

    /**
     * @var Address|null L'adresse physique de la personne.
     */
    #[ORM\Embedded(class: Address::class, columnPrefix: 'address_')]
    protected ?Address $address = null;

    /**
     * Récupère le nom complet.
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * Définit le nom complet.
     */
    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * Récupère le numéro de téléphone.
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Définit le numéro de téléphone.
     */
    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Récupère le genre.
     */
    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    /**
     * Définit le genre.
     */
    public function setGender(?Gender $gender): static
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * Récupère l'URL de l'avatar.
     */
    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    /**
     * Définit l'URL de l'avatar.
     */
    public function setAvatarUrl(?string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;
        return $this;
    }

    /**
     * Récupère l'adresse.
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * Définit l'adresse.
     */
    public function setAddress(?Address $address): static
    {
        $this->address = $address;
        return $this;
    }
}
