<?php

namespace App\Entity\Medical;

use App\Entity\Common\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'laboratory_results')]
class LaboratoryResult extends BaseEntity
{
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $testName = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $fileUrl = null;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $labName = null;

    public function getTestName(): ?string
    {
        return $this->testName;
    }

    public function setTestName(string $testName): self
    {
        $this->testName = $testName;
        return $this;
    }

    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    public function setFileUrl(?string $fileUrl): self
    {
        $this->fileUrl = $fileUrl;
        return $this;
    }

    public function getLabName(): ?string
    {
        return $this->labName;
    }

    public function setLabName(?string $labName): self
    {
        $this->labName = $labName;
        return $this;
    }
}
