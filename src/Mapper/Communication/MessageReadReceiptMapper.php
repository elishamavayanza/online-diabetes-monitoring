<?php

namespace App\Mapper\Communication;

use App\DTO\Request\Communication\MessageReadReceiptRequestDTO;
use App\DTO\Response\Communication\MessageReadReceiptResponseDTO;
use App\Entity\Communication\MessageReadReceipt;
use App\Entity\Communication\Message;
use App\Entity\Identity\User;

class MessageReadReceiptMapper
{
    public function mapRequestToEntity(
        MessageReadReceiptRequestDTO $dto,
        Message $message,
        User $participant,
        ?MessageReadReceipt $receipt = null
    ): MessageReadReceipt {
        $receipt ??= new MessageReadReceipt();

        $receipt->setMessage($message);
        // `MessageReadReceipt` stocke un `user` (patient ou soignant).
        $receipt->setUser($participant);
        $receipt->setReadAt($dto->readAt);

        return $receipt;
    }

    public function mapEntityToResponse(MessageReadReceipt $receipt): MessageReadReceiptResponseDTO
    {
        return MessageReadReceiptResponseDTO::fromEntity($receipt);
    }
}
