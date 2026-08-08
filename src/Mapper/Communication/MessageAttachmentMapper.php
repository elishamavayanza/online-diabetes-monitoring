<?php

namespace App\Mapper\Communication;

use App\DTO\Request\Communication\MessageAttachmentRequestDTO;
use App\DTO\Response\Communication\MessageAttachmentResponseDTO;
use App\Entity\Communication\MessageAttachment;
use App\Entity\Communication\Message;

class MessageAttachmentMapper
{
    public function mapRequestToEntity(
        MessageAttachmentRequestDTO $dto,
        Message $message,
        ?MessageAttachment $attachment = null
    ): MessageAttachment {
        $attachment ??= new MessageAttachment();

        $attachment->setMessage($message);
        $attachment->setFileUrl($dto->fileUrl);
        $attachment->setFileName($dto->fileName);
        $attachment->setMimeType($dto->mimeType);
        $attachment->setSizeBytes($dto->sizeBytes);

        return $attachment;
    }

    public function mapEntityToResponse(MessageAttachment $attachment): MessageAttachmentResponseDTO
    {
        return MessageAttachmentResponseDTO::fromEntity($attachment);
    }
}
