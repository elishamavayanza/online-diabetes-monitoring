<?php

namespace App\Mapper\Treatment;

use App\DTO\Request\Treatment\PrescriptionItemRequestDTO;
use App\DTO\Response\Treatment\PrescriptionItemResponseDTO;
use App\Entity\Identity\User;
use App\Entity\Treatment\PrescriptionItem;
use App\Entity\Treatment\Prescription;
use App\Entity\Treatment\Medication;

class PrescriptionItemMapper
{
    public function mapRequestToEntity(
        PrescriptionItemRequestDTO $dto,
        Prescription $prescription,
        Medication $medication,
        ?User $createdBy = null,
        ?PrescriptionItem $item = null
    ): PrescriptionItem {
        $isNew = $item === null;
        $item ??= new PrescriptionItem();

        if ($isNew && $createdBy !== null) {
            $item->setCreatedBy($createdBy);
        }

        $item->setPrescription($prescription);
        $item->setMedication($medication);
        $item->setDosage($dto->dosage);
        $item->setQuantity($dto->quantity);
        $item->setMorning($dto->morning);
        $item->setNoon($dto->noon);
        $item->setEvening($dto->evening);
        $item->setInstructions($dto->instructions);

        return $item;
    }

    public function mapEntityToResponse(PrescriptionItem $item): PrescriptionItemResponseDTO
    {
        return PrescriptionItemResponseDTO::fromEntity($item);
    }

    /**
     * @param iterable<PrescriptionItem> $items
     * @return array<PrescriptionItemResponseDTO>
     */
    public function mapEntitiesToResponses(iterable $items): array
    {
        $responses = [];
        foreach ($items as $item) {
            $responses[] = $this->mapEntityToResponse($item);
        }
        return $responses;
    }
}
