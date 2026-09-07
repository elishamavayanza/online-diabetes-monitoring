<?php

namespace App\Entity\Treatment;

use App\Entity\Common\BaseEntity;
use App\Entity\Identity\Patient;
use App\Entity\Identity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une injection d'insuline réalisée par un patient.
 *
 * Il s'agit d'un événement de traitement (et non d'une mesure médicale) :
 * c'est pourquoi cette entité n'hérite pas de PatientCommonOperation.
 */
#[ORM\Entity]
#[ORM\Table(name: 'treatment_insulin_injections')]
class InsulinInjection extends BaseEntity
{
    /**
     * @var Patient|null Le patient ayant réalisé l'injection.
     */
    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(name: 'patient_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Patient $patient = null;

    /**
     * @var PrescriptionItem|null L'élément de prescription associé à l'injection.
     */
    #[ORM\ManyToOne(targetEntity: PrescriptionItem::class, inversedBy: 'injections')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?PrescriptionItem $prescriptionItem = null;

    /**
     * @var Insulin|null Les informations spécifiques de l'insuline injectée.
     */
    #[ORM\ManyToOne(targetEntity: Insulin::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Insulin $insulin = null;

    /**
     * @var \DateTimeImmutable|null La date et l'heure de l'injection.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $injectedAt = null;

    /**
     * @var string|null La dose d'insuline injectée, exprimée en unités.
     */
    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    private ?string $doseUnits = null;

    /**
     * @var InjectionSite|null Le site d'injection utilisé.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: InjectionSite::class)]
    private ?InjectionSite $injectionSite = null;

    /**
     * @var IntakeStatus|null Le statut de l'injection (pris, sauté, retardé).
     */
    #[ORM\Column(type: 'string', length: 50, enumType: IntakeStatus::class)]
    private ?IntakeStatus $status = null;

    /**
     * @var User|null L'utilisateur ayant enregistré l'injection.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'issuer_id', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    private ?User $issuer = null;

    /**
     * @var string|null Notes ou observations complémentaires concernant l'injection.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    /**
     * Récupère le patient associé.
     */
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    /**
     * Définit le patient associé.
     */
    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    /**
     * Récupère l'élément de prescription associé.
     */
    public function getPrescriptionItem(): ?PrescriptionItem
    {
        return $this->prescriptionItem;
    }

    /**
     * Définit l'élément de prescription associé.
     */
    public function setPrescriptionItem(?PrescriptionItem $prescriptionItem): static
    {
        $this->prescriptionItem = $prescriptionItem;
        return $this;
    }

    /**
     * Récupère l'insuline injectée.
     */
    public function getInsulin(): ?Insulin
    {
        return $this->insulin;
    }

    /**
     * Définit l'insuline injectée.
     */
    public function setInsulin(?Insulin $insulin): static
    {
        $this->insulin = $insulin;
        return $this;
    }

    /**
     * Récupère la date et l'heure de l'injection.
     */
    public function getInjectedAt(): ?\DateTimeImmutable
    {
        return $this->injectedAt;
    }

    /**
     * Définit la date et l'heure de l'injection.
     */
    public function setInjectedAt(\DateTimeImmutable $injectedAt): static
    {
        $this->injectedAt = $injectedAt;
        return $this;
    }

    /**
     * Récupère la dose en unités.
     */
    public function getDoseUnits(): ?string
    {
        return $this->doseUnits;
    }

    /**
     * Définit la dose en unités.
     */
    public function setDoseUnits(string $doseUnits): static
    {
        $this->doseUnits = $doseUnits;
        return $this;
    }

    /**
     * Récupère le site d'injection.
     */
    public function getInjectionSite(): ?InjectionSite
    {
        return $this->injectionSite;
    }

    /**
     * Définit le site d'injection.
     */
    public function setInjectionSite(InjectionSite $injectionSite): static
    {
        $this->injectionSite = $injectionSite;
        return $this;
    }

    /**
     * Récupère le statut de l'injection.
     */
    public function getStatus(): ?IntakeStatus
    {
        return $this->status;
    }

    /**
     * Définit le statut de l'injection.
     */
    public function setStatus(IntakeStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Récupère l'utilisateur ayant enregistré l'injection.
     */
    public function getIssuer(): ?User
    {
        return $this->issuer;
    }

    /**
     * Définit l'utilisateur ayant enregistré l'injection.
     */
    public function setIssuer(?User $issuer): static
    {
        $this->issuer = $issuer;
        return $this;
    }

    /**
     * Récupère les notes complémentaires.
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * Définit les notes complémentaires.
     */
    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }
}
