<?php

namespace App\Mapper\Treatment;

use App\DTO\Request\Treatment\PrescriptionVersionRequestDTO;
use App\DTO\Response\Treatment\PrescriptionVersionResponseDTO;
use App\Entity\Treatment\PrescriptionVersion;
use App\Entity\Treatment\Prescription;
use App\Entity\Identity\User;

class PrescriptionVersionMapper
{
    public function mapRequestToEntity(
        PrescriptionVersionRequestDTO $dto,
        Prescription $prescription,
        User $modifiedBy,
        ?PrescriptionVersion $version = null
    ): PrescriptionVersion {
        $version ??= new PrescriptionVersion();

        $version->setPrescription($prescription);
        $version->setVersionNumber($dto->versionNumber);
        $version->setChangesSummary($dto->changesSummary);
        $version->setData($dto->data);
        $version->setModifiedBy($modifiedBy);
        $version->setModifiedAt($dto->modifiedAt);

        return $version;
    }

    public function mapEntityToResponse(PrescriptionVersion $version): PrescriptionVersionResponseDTO
    {
        return PrescriptionVersionResponseDTO::fromEntity($version);
    }

    /**
     * @param iterable<PrescriptionVersion> $versions
     * @return array<PrescriptionVersionResponseDTO>
     */
    public function mapEntitiesToResponses(iterable $versions): array
    {
        $responses = [];
        foreach ($versions as $version) {
            $responses[] = $this->mapEntityToResponse($version);
        }
        return $responses;
    }
}
