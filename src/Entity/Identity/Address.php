<?php

namespace App\Entity\Identity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une adresse physique intégrable (Embeddable) au sein d'autres entités.
 * Note : Un #[ORM\Table] sur une classe #[ORM\Embeddable] n'est pas utilisé par Doctrine
 * (les colonnes sont fusionnées dans la table de l'entité parente via #[ORM\Embedded]),
 * mais les annotations de structure et de documentation restent en place.
 */
#[ORM\Embeddable]
#[ORM\Table(name: 'identity_address')]
class Address
{
    /**
     * @var string|null La rue ou le libellé de la voie.
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $street = null;

    /**
     * @var string|null La ville ou la localité.
     */
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $city = null;

    /**
     * @var string|null Le code postal.
     */
    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $postalCode = null;

    /**
     * @var string|null Le pays.
     */
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $state = null;

    /**
     * Récupère la rue.
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * Définit la rue.
     */
    public function setStreet(?string $street): static
    {
        $this->street = $street;
        return $this;
    }

    /**
     * Récupère la ville.
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Définit la ville.
     */
    public function setCity(?string $city): static
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Récupère le code postal.
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * Définit le code postal.
     */
    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * Récupère le pays.
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Définit le pays.
     */
    public function setCountry(?string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;
        return $this;
    }

}
