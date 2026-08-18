<?php

namespace App\Mapper\Common;

use App\DTO\Response\Common\FileAttachmentResponseDTO;
use App\Entity\Common\FileAttachment;
use App\Repository\Identity\UserRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileAttachmentMapper
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function mapUploadToEntity(
        UploadedFile $file,
        string $uniqueFilename,
        string $entityType,
        string $entityId,
        ?string $currentUserId = null
    ): FileAttachment {
        $fileAttachment = new FileAttachment();

        $fileAttachment->setOriginalName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fileAttachment->setFilename($uniqueFilename);
        $fileAttachment->setMimeType($file->getMimeType());
        $fileAttachment->setSizeBytes($file->getSize());

        // Construction de l'URL ou chemin d'accès public relatif
        $subFolder = str_starts_with((string)$file->getMimeType(), 'audio/') ? 'voices' : 'attachments';
        $fileAttachment->setUrl('/uploads/' . $subFolder . '/' . $uniqueFilename);

        $fileAttachment->setEntityType($entityType);
        $fileAttachment->setEntityId($entityId);

        if ($currentUserId) {
            $user = $this->userRepository->find($currentUserId);
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
