<?php

namespace App\Mapper\Common;

use App\DTO\Request\Common\FileAttachmentRequestDTO;
use App\DTO\Response\Common\FileAttachmentResponseDTO;
use App\Entity\Common\FileAttachment;
use App\Repository\Identity\UserRepository;

class FileAttachmentMapper
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function mapRequestToEntity(FileAttachmentRequestDTO $dto, ?FileAttachment $entity = null): FileAttachment
    {
        $fileAttachment = $entity ?? new FileAttachment();

        $fileAttachment->setOriginalName($dto->originalName);
        $fileAttachment->setFilename($dto->fileName);
        $fileAttachment->setMimeType($dto->mimeType);
        $fileAttachment->setSizeBytes($dto->sizeBytes);
        $fileAttachment->setUrl($dto->url);
        $fileAttachment->setEntityType($dto->entityType);
        $fileAttachment->setEntityId($dto->entityId);

        if ($dto->uploadedById) {
            $user = $this->userRepository->find($dto->uploadedById);
            if ($user) {
                $fileAttachment->setUploadedBy($user);
            }
        }

        return $fileAttachment;
    }

    public function mapEntityToResponse(FileAttachment $fileAttachment): FileAttachmentResponseDTO
    {
        return FileAttachmentResponseDTO::fromEntity($fileAttachment);
    }
}
