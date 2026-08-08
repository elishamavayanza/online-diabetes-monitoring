<?php

namespace App\Mapper\Communication;

use App\DTO\Request\Communication\ConversationParticipantRequestDTO;
use App\DTO\Response\Communication\ConversationParticipantResponseDTO;
use App\Entity\Communication\ConversationParticipant;
use App\Entity\Communication\Conversation;
use App\Entity\Identity\User;

class ConversationParticipantMapper
{
    public function mapRequestToEntity(
        ConversationParticipantRequestDTO $dto,
        Conversation $conversation,
        User $user,
        ?ConversationParticipant $participant = null
    ): ConversationParticipant {
        $participant ??= new ConversationParticipant();

        $participant->setConversation($conversation);
        $participant->setUser($user);
        $participant->setJoinedAt($dto->joinedAt);
        $participant->setLeftAt($dto->leftAt);

        return $participant;
    }

    public function mapEntityToResponse(ConversationParticipant $participant): ConversationParticipantResponseDTO
    {
        return ConversationParticipantResponseDTO::fromEntity($participant);
    }
}
