<?php

namespace App\Service\Common;

use App\DTO\Feedback;
use App\DTO\Request\Common\FileAttachmentRequestDTO;
use App\Mapper\Common\FileAttachmentMapper;
use App\Repository\Common\FileAttachmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class FileAttachmentService
{
    public function __construct(
        private readonly FileAttachmentRepository $fileAttachmentRepository,
        private readonly FileAttachmentMapper $fileAttachmentMapper,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function create(FileAttachmentRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $fileAttachment = $this->fileAttachmentMapper->mapRequestToEntity($dto);

            $this->entityManager->persist($fileAttachment);
            $this->entityManager->flush();

            $responseDTO = $this->fileAttachmentMapper->mapEntityToResponse($fileAttachment);

            $feedback->setFlushDescription("Fichier attaché avec succès.")
                ->autoInitFlush();

            // On peut injecter le DTO de réponse dans les données ou gérer via une surcouche
            // Pour l'exemple, on s'appuie sur la structure du feedback
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur lors de l'enregistrement du fichier : " . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }

    public function getById(string $id): ?Feedback
    {
        $feedback = new Feedback();
        $fileAttachment = $this->fileAttachmentRepository->find($id);

        if (!$fileAttachment) {
            $feedback->setErrorFlushDescription("Fichier introuvable.")
                ->autoInitFlush();
            return $feedback;
        }

        $feedback->setFlushDescription("Fichier récupéré avec succès.")
            ->autoInitFlush();

        return $feedback;
    }
}
