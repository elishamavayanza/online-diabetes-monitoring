<?php

namespace App\Entity\Medical;

use App\Entity\Common\PatientCommonOperation;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente les résultats d'un examen de laboratoire pour un patient.
 */
#[ORM\Entity]
#[ORM\Table(name: 'medical_laboratory_results')]
class LaboratoryResult extends PatientCommonOperation
{
    /**
     * @var string|null Le nom de l'examen ou du test de laboratoire.
     */
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $testName = null;

    /**
     * @var string|null L'URL ou le chemin d'accès au fichier des résultats.
     */
    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $fileUrl = null;

    /**
     * @var string|null Le nom du laboratoire ayant effectué l'analyse.
     */
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $labName = null;

    /**
     * Récupère le nom du test.
     */
    public function getTestName(): ?string
    {
        return $this->testName;
    }

    /**
     * Définit le nom du test.
     */
    public function setTestName(string $testName): static
    {
        $this->testName = $testName;
        return $this;
    }

    /**
     * Récupère l'URL du fichier.
     */
    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    /**
     * Définit l'URL du fichier.
     */
    public function setFileUrl(?string $fileUrl): static
    {
        $this->fileUrl = $fileUrl;
        return $this;
    }

    /**
     * Récupère le nom du laboratoire.
     */
    public function getLabName(): ?string
    {
        return $this->labName;
    }

    /**
     * Définit le nom du laboratoire.
     */
    public function setLabName(?string $labName): static
    {
        $this->labName = $labName;
        return $this;
    }
}
