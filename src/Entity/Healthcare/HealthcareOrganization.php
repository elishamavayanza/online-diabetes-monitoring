<?php

namespace App\Entity\Healthcare;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Address;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une organisation de santé (réseau, hôpital, groupe médical ou institution)
 * au sein du système.
 */
#[ORM\Entity]
#[ORM\Table(name: 'healthcare_organizations')]
class HealthcareOrganization extends BaseEntity
{
    /**
     * @var string|null Le nom complet de l'organisation.
     */
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    /**
     * @var string|null Le nom abrégé ou sigle de l'organisation.
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $shortName = null;

    /**
     * @var OrganizationType|null Le type d'organisation de santé.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: OrganizationType::class)]
    private ?OrganizationType $type = null;

    /**
     * @var string|null L'adresse e-mail de contact de l'organisation.
     */
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    private ?string $email = null;

    /**
     * @var string|null Le numéro de téléphone de l'organisation.
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $phone = null;

    /**
     * @var string|null Le site web officiel de l'organisation.
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $website = null;

    /**
     * @var string|null L'URL ou le chemin d'accès vers le logo de l'organisation.
     */
    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $logoUrl = null;

    /**
     * @var Address|null L'adresse physique principale de l'organisation.
     */
    #[ORM\Embedded(class: Address::class, columnPrefix: 'address_')]
    private ?Address $address = null;

    /**
     * @var bool Indique si l'organisation est active.
     */
    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    /**
     * @var Collection<int, HealthcareFacility> La liste des établissements rattachés à cette organisation.
     */
    #[ORM\OneToMany(mappedBy: 'organization', targetEntity: HealthcareFacility::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $facilities;

    /**
     * @var Collection<int, OrganizationMembership> La liste des adhésions ou appartenances des utilisateurs à cette organisation.
     */
    #[ORM\OneToMany(mappedBy: 'organization', targetEntity: OrganizationMembership::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $memberships;

    /**
     * Initialise les collections d'établissements et de membres.
     */
    public function __construct()
    {
        $this->facilities = new ArrayCollection();
        $this->memberships = new ArrayCollection();
    }

    /**
     * Récupère le nom de l'organisation.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de l'organisation.
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Récupère le nom abrégé.
     */
    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    /**
     * Définit le nom abrégé.
     */
    public function setShortName(?string $shortName): static
    {
        $this->shortName = $shortName;
        return $this;
    }

    /**
     * Récupère le type de l'organisation.
     */
    public function getType(): ?OrganizationType
    {
        return $this->type;
    }

    /**
     * Définit le type de l'organisation.
     */
    public function setType(OrganizationType $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Récupère l'e-mail de contact.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'e-mail de contact.
     */
    public function setEmail(?string $email): static
    {
        $this->email = $email;
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
     * Récupère le site web.
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * Définit le site web.
     */
    public function setWebsite(?string $website): static
    {
        $this->website = $website;
        return $this;
    }

    /**
     * Récupère l'URL du logo.
     */
    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    /**
     * Définit l'URL du logo.
     */
    public function setLogoUrl(?string $logoUrl): static
    {
        $this->logoUrl = $logoUrl;
        return $this;
    }

    /**
     * Récupère l'adresse de l'organisation.
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * Définit l'adresse de l'organisation.
     */
    public function setAddress(?Address $address): static
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Indique si l'organisation est active.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Modifie l'état d'activité de l'organisation.
     */
    public function setActive(bool $active): static
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Retourne la collection des établissements.
     *
     * @return Collection<int, HealthcareFacility>
     */
    public function getFacilities(): Collection
    {
        return $this->facilities;
    }

    /**
     * Ajoute un établissement à l'organisation.
     */
    public function addFacility(HealthcareFacility $facility): static
    {
        if (!$this->facilities->contains($facility)) {
            $this->facilities->add($facility);
            $facility->setOrganization($this);
        }
        return $this;
    }

    /**
     * Retire un établissement de l'organisation.
     */
    public function removeFacility(HealthcareFacility $facility): static
    {
        if ($this->facilities->removeElement($facility)) {
            if ($facility->getOrganization() === $this) {
                $facility->setOrganization(null);
            }
        }
        return $this;
    }

    /**
     * Retourne la collection des adhésions/membres.
     *
     * @return Collection<int, OrganizationMembership>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    /**
     * Ajoute une adhésion à l'organisation.
     */
    public function addMembership(OrganizationMembership $membership): static
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships->add($membership);
            $membership->setOrganization($this);
        }
        return $this;
    }

    /**
     * Retire une adhésion de l'organisation.
     */
    public function removeMembership(OrganizationMembership $membership): static
    {
        if ($this->memberships->removeElement($membership)) {
            if ($membership->getOrganization() === $this) {
                $membership->setOrganization(null);
            }
        }
        return $this;
    }
}
