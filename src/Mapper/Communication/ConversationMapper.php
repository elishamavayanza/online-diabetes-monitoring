<?php

namespace App\Mapper\Communication;

use App\DTO\Request\Communication\ConversationRequestDTO;
use App\DTO\Response\Communication\ConversationResponseDTO;
use App\Entity\Communication\Conversation;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\Patient;
use App\Entity\Identity\User;

class ConversationMapper
{
    public function mapRequestToEntity(
        ConversationRequestDTO $dto,
        User $createdBy,
        Patient $patient,
        ?HealthcareOrganization $organization = null,
        ?Conversation $conversation = null
    ): Conversation {
        $conversation ??= new Conversation();

        $conversation->setSubject($dto->subject);
        $conversation->setPatient($patient);
        $conversation->setOrganization($organization);
        $conversation->setCreatedBy($createdBy);
        $conversation->setClosedAt($dto->closedAt);

        return $conversation;
    }

    public function mapEntityToResponse(Conversation $conversation): ConversationResponseDTO
    {
        return ConversationResponseDTO::fromEntity($conversation);
    }
}
