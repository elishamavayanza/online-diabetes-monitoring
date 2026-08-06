<?php

namespace App\Entity\Healthcare;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Address;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'healthcare_organizations')]
class HealthcareOrganization extends BaseEntity
{
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $shortName = null;

    #[ORM\Column(type: 'string', length: 50, enumType: OrganizationType::class)]
    private ?OrganizationType $type = null;

    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $logoUrl = null;

    #[ORM\Embedded(class: Address::class, columnPrefix: 'address_')]
    private ?Address $address = null;

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    #[ORM\OneToMany(mappedBy: 'organization', targetEntity: HealthcareFacility::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $facilities;

    #[ORM\OneToMany(mappedBy: 'organization', targetEntity: OrganizationMembership::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $memberships;

    public function __construct()
    {
        parent::__construct();
        $this->facilities = new ArrayCollection();
        $this->memberships = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(?string $shortName): self
    {
        $this->shortName = $shortName;
        return $this;
    }

    public function getType(): ?OrganizationType
    {
        return $this->type;
    }

    public function setType(OrganizationType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(?string $logoUrl): self
    {
        $this->logoUrl = $logoUrl;
        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return Collection<int, HealthcareFacility>
     */
    public function getFacilities(): Collection
    {
        return $this->facilities;
    }

    public function addFacility(HealthcareFacility $facility): self
    {
        if (!$this->facilities->contains($facility)) {
            $this->facilities->add($facility);
            $facility->setOrganization($this);
        }
        return $this;
    }

    public function removeFacility(HealthcareFacility $facility): self
    {
        if ($this->facilities->removeElement($facility)) {
            if ($facility->getOrganization() === $this) {
                $facility->setOrganization(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, OrganizationMembership>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(OrganizationMembership $membership): self
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships->add($membership);
            $membership->setOrganization($this);
        }
        return $this;
    }

    public function removeMembership(OrganizationMembership $membership): self
    {
        if ($this->memberships->removeElement($membership)) {
            if ($membership->getOrganization() === $this) {
                $membership->setOrganization(null);
            }
        }
        return $this;
    }
}
