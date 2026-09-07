<?php

namespace App\Entity\Treatment;

use App\Entity\Common\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un médicament avec ses caractéristiques et sa catégorie.
 */
#[ORM\Entity]
#[ORM\Table(name: 'treatment_medications')]
class Medication extends BaseEntity
{
    /**
     * @var string|null Le nom du médicament.
     */
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name = null;

    /**
     * @var MedicationClass|null La classe du médicament (Insuline ou Médicament général).
     */
    #[ORM\Column(type: 'string', length: 50, enumType: MedicationClass::class)]
    private ?MedicationClass $category = null;

    /**
     * @var MedicationForm|null La forme galénique du médicament (réservée aux médicaments généraux : Comprimé ou Liquide).
     */
    #[ORM\Column(type: 'string', length: 50, enumType: MedicationForm::class, nullable: true)]
    private ?MedicationForm $form = null;

    /**
     * @var bool Indique si le médicament est actif dans le référentiel.
     */
    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    /**
     * @var string|null La description détaillée du médicament.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /**
     * @var string|null Le fabricant ou le laboratoire pharmaceutique.
     */
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $manufacturer = null;

    /**
     * @var Collection<int, Insulin> La collection des informations d'insuline associées à ce médicament.
     */
    #[ORM\OneToMany(mappedBy: 'medication', targetEntity: Insulin::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $insulins;

    /**
     * Constructeur pour initialiser la collection des insulines.
     */
    public function __construct()
    {
        $this->insulins = new ArrayCollection();
    }

    /**
     * Récupère le nom du médicament.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom du médicament.
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Récupère la classe du médicament.
     */
    public function getCategory(): ?MedicationClass
    {
        return $this->category;
    }

    /**
     * Définit la classe du médicament.
     */
    public function setCategory(MedicationClass $category): static
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Récupère la forme galénique du médicament.
     */
    public function getForm(): ?MedicationForm
    {
        return $this->form;
    }

    /**
     * Définit la forme galénique du médicament.
     */
    public function setForm(?MedicationForm $form): static
    {
        $this->form = $form;
        return $this;
    }

    /**
     * Récupère le statut actif du médicament.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Définit le statut actif du médicament.
     */
    public function setActive(bool $active): static
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Récupère la description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description.
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Récupère le fabricant.
     */
    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    /**
     * Définit le fabricant.
     */
    public function setManufacturer(?string $manufacturer): static
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    /**
     * Récupère la collection des insulines associées.
     *
     * @return Collection<int, Insulin>
     */
    public function getInsulins(): Collection
    {
        return $this->insulins;
    }

    /**
     * Ajoute une insuline au médicament.
     */
    public function addInsulin(Insulin $insulin): static
    {
        if (!$this->insulins->contains($insulin)) {
            $this->insulins->add($insulin);
            $insulin->setMedication($this);
        }
        return $this;
    }

    /**
     * Retire une insuline du médicament.
     */
    public function removeInsulin(Insulin $insulin): static
    {
        if ($this->insulins->removeElement($insulin)) {
            if ($insulin->getMedication() === $this) {
                $insulin->setMedication(null);
            }
        }
        return $this;
    }
}
