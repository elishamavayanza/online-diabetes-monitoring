<?php

namespace App\Entity\Healthcare;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Address;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un établissement de santé physique (hôpital, clinique, centre médical)
 * rattaché à une organisation de santé.
 */
#[ORM\Entity]
#[ORM\Table(name: 'healthcare_facilities')]
class HealthcareFacility extends BaseEntity
{
    /**
     * @var HealthcareOrganization|null L'organisation de santé parente qui gère cet établissement.
     */
    #[ORM\ManyToOne(targetEntity: HealthcareOrganization::class, inversedBy: 'facilities')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?HealthcareOrganization $organization = null;

    /**
     * @var string|null Le nom de l'établissement de santé.
     */
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    /**
     * @var Address|null L'adresse physique de l'établissement.
     */
    #[ORM\Embedded(class: Address::class, columnPrefix: 'address_')]
    private ?Address $address = null;

    /**
     * @var string|null Le numéro de téléphone principal de l'établissement.
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $phone = null;

    /**
     * @var Collection<int, Department> La liste des départements ou services médicaux de cet établissement.
     */
    #[ORM\OneToMany(mappedBy: 'facility', targetEntity: Department::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $departments;

    /**
     * Initialise la collection des départements.
     */
    public function __construct()
    {
        $this->departments = new ArrayCollection();
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
     * Récupère le nom de l'établissement.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de l'établissement.
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Récupère l'adresse de l'établissement.
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * Définit l'adresse de l'établissement.
     */
    public function setAddress(?Address $address): static
    {
        $this->address = $address;
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
     * Retourne la collection des départements.
     *
     * @return Collection<int, Department>
     */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    /**
     * Ajoute un département à l'établissement.
     */
    public function addDepartment(Department $department): static
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($department);
            $department->setFacility($this);
        }
        return $this;
    }

    /**
     * Retire un département de l'établissement.
     */
    public function removeDepartment(Department $department): static
    {
        if ($this->departments->removeElement($department)) {
            if ($department->getFacility() === $this) {
                $department->setFacility(null);
            }
        }
        return $this;
    }
}
