<?php

namespace App\Mapper\Communication;

use App\DTO\Request\Communication\MessageRequestDTO;
use App\DTO\Response\Communication\MessageResponseDTO;
use App\Entity\Communication\Message;
use App\Entity\Communication\Conversation;
use App\Entity\Identity\User;

class MessageMapper
{
    public function mapRequestToEntity(
        MessageRequestDTO $dto,
        Conversation $conversation,
        User $sender,
        ?Message $message = null
    ): Message {
        $message ??= new Message();

        $message->setConversation($conversation);
        $message->setSender($sender);
        $message->setContent($dto->content);
        $message->setSentAt($dto->sentAt);
        $message->setEditedAt($dto->editedAt);

        return $message;
    }

    public function mapEntityToResponse(Message $message): MessageResponseDTO
    {
        return MessageResponseDTO::fromEntity($message);
    }
}
