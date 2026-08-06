<?php

namespace App\Entity\Healthcare;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Address;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'healthcare_facilities')]
class HealthcareFacility extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class, inversedBy: 'facilities')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareOrganization $organization = null;

    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    #[ORM\Embedded(class: Address::class, columnPrefix: 'address_')]
    private ?Address $address = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'facility', targetEntity: Department::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $departments;

    public function __construct()
    {
        parent::__construct();
        $this->departments = new ArrayCollection();
    }

    public function getOrganization(): ?HealthcareOrganization
    {
        return $this->organization;
    }

    public function setOrganization(?HealthcareOrganization $organization): self
    {
        $this->organization = $organization;
        return $this;
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

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;
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

    /**
     * @return Collection<int, Department>
     */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): self
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($department);
            $department->setFacility($this);
        }
        return $this;
    }

    public function removeDepartment(Department $department): self
    {
        if ($this->departments->removeElement($department)) {
            if ($department->getFacility() === $this) {
                $department->setFacility(null);
            }
        }
        return $this;
    }
}
